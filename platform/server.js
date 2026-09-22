const http = require('http');
const net = require('net');
const { spawn, spawnSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const appRoot = fs.existsSync(path.join(__dirname, 'artisan'))
  ? __dirname
  : path.join(__dirname, 'platform');
const publicDir = path.join(appRoot, 'public');
const router = path.join(appRoot, 'vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php');

function findPhp() {
  const candidates = [
    process.env.PHP_BIN,
    'php',
    '/opt/alt/php83/usr/bin/php',
    '/opt/alt/php84/usr/bin/php',
    '/opt/alt/php82/usr/bin/php',
    '/usr/bin/php',
  ].filter(Boolean);

  for (const bin of candidates) {
    const result = spawnSync(bin, ['-r', 'echo PHP_MAJOR_VERSION, ".", PHP_MINOR_VERSION;'], { encoding: 'utf8' });
    if (result.status === 0 && /^\d+\.\d+/.test(result.stdout || '')) {
      return bin;
    }
  }

  return null;
}

function parseEnvFile(file) {
  const values = {};
  let text = '';
  try {
    text = fs.readFileSync(file, 'utf8');
  } catch {
    return values;
  }
  for (const line of text.split(/\r?\n/)) {
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith('#')) continue;
    const eq = trimmed.indexOf('=');
    if (eq < 1) continue;
    let value = trimmed.slice(eq + 1).trim();
    if ((value.startsWith('"') && value.endsWith('"')) || (value.startsWith("'") && value.endsWith("'"))) {
      value = value.slice(1, -1);
    }
    values[trimmed.slice(0, eq).trim()] = value;
  }
  return values;
}

function findEnvFiles() {
  const files = [];
  for (const start of [process.cwd(), appRoot]) {
    const chain = [];
    let dir = start;
    for (let i = 0; i < 8; i += 1) {
      chain.push(dir);
      const parent = path.dirname(dir);
      if (parent === dir) break;
      dir = parent;
    }
    for (const folder of chain.reverse()) {
      for (const rel of ['.env', 'platform/.env', 'public_html/.env', 'public_html/platform/.env']) {
        const file = path.join(folder, rel);
        if (fs.existsSync(file)) files.push(file);
      }
    }
  }
  return [...new Set(files)];
}

function envForPhp() {
  const env = { ...process.env };
  for (const file of findEnvFiles()) {
    for (const [key, value] of Object.entries(parseEnvFile(file))) {
      if (!env[key] && value !== '') env[key] = value;
    }
  }
  const aliases = {
    MYSQL_DATABASE: 'DB_DATABASE',
    MYSQL_USER: 'DB_USERNAME',
    MYSQL_PASSWORD: 'DB_PASSWORD',
    MYSQL_HOST: 'DB_HOST',
  };
  for (const [from, to] of Object.entries(aliases)) {
    if (!env[to] && env[from]) env[to] = env[from];
  }
  const smashed = env.DB_CONNECTION || '';
  if (/\s/.test(smashed)) {
    const parts = smashed.split(/\s+/);
    env.DB_CONNECTION = parts[0];
    for (const part of parts.slice(1)) {
      const eq = part.indexOf('=');
      if (eq > 0 && !env[part.slice(0, eq)]) {
        env[part.slice(0, eq)] = part.slice(eq + 1);
      }
    }
  }
  if (!env.DB_CONNECTION) env.DB_CONNECTION = 'mysql';
  if (!env.DB_HOST || env.DB_HOST === '127.0.0.1') env.DB_HOST = 'localhost';
  if (!env.DB_PORT) env.DB_PORT = '3306';
  env.PHP_CLI_SERVER_WORKERS = env.PHP_CLI_SERVER_WORKERS || '4';
  return env;
}

function freePort() {
  return new Promise((resolve, reject) => {
    const probe = net.createServer();
    probe.once('error', reject);
    probe.listen(0, '127.0.0.1', () => {
      const { port } = probe.address();
      probe.close(() => resolve(port));
    });
  });
}

function waitForPhp(port) {
  return new Promise((resolve, reject) => {
    const attempt = (left) => {
      const socket = net.connect(port, '127.0.0.1');
      socket.once('connect', () => {
        socket.end();
        resolve();
      });
      socket.once('error', () => {
        socket.destroy();
        if (left <= 0) {
          reject(new Error('PHP did not start'));
          return;
        }
        setTimeout(() => attempt(left - 1), 100);
      });
    };
    attempt(50);
  });
}

function artisan(php, args, env) {
  return new Promise((resolve) => {
    const child = spawn(php, ['artisan', ...args], { cwd: appRoot, env, stdio: 'inherit' });
    const timer = setTimeout(() => {
      child.kill('SIGTERM');
      resolve(1);
    }, 90000);
    child.on('exit', (code) => {
      clearTimeout(timer);
      resolve(code ?? 1);
    });
  });
}

function userCount(php, env) {
  const result = spawnSync(php, ['-r', `
    require 'vendor/autoload.php';
    $app = require 'bootstrap/app.php';
    $app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();
    echo (string) App\\Models\\User::query()->count();
  `], { cwd: appRoot, env, encoding: 'utf8' });
  return Number.parseInt(result.stdout || '0', 10) || 0;
}

function listen(port, handler) {
  const server = http.createServer(handler);
  server.listen(port, '0.0.0.0');
  return server;
}

function proxyTo(phpPort, req, res) {
  const headers = { ...req.headers };
  headers['x-forwarded-proto'] = headers['x-forwarded-proto'] || 'https';
  headers['x-forwarded-for'] = headers['x-forwarded-for'] || req.socket.remoteAddress || '';
  const upstream = http.request({
    host: '127.0.0.1',
    port: phpPort,
    method: req.method,
    path: req.url,
    headers,
  }, (upstreamRes) => {
    res.writeHead(upstreamRes.statusCode || 502, upstreamRes.headers);
    upstreamRes.pipe(res);
  });
  upstream.on('error', () => {
    if (!res.headersSent) {
      res.writeHead(502, { 'Content-Type': 'text/plain; charset=UTF-8' });
    }
    res.end('Laravel is not responding.');
  });
  req.pipe(upstream);
}

async function main() {
  const port = Number(process.env.PORT) || 3000;
  const php = findPhp();
  if (!php || !fs.existsSync(router)) {
    listen(port, (_req, res) => {
      res.writeHead(500, { 'Content-Type': 'text/plain; charset=UTF-8' });
      res.end(php ? 'Laravel router is missing.\n' : 'PHP is not installed next to Node, so Laravel cannot start.\n');
    });
    console.error(php ? 'Missing Laravel router' : 'PHP binary not found');
    return;
  }

  const env = envForPhp();
  const phpPort = await freePort();
  const child = spawn(php, ['-S', `127.0.0.1:${phpPort}`, router], {
    cwd: publicDir,
    env,
    stdio: 'inherit',
  });
  const stop = () => {
    if (!child.killed) {
      child.kill('SIGTERM');
    }
  };
  process.on('SIGTERM', () => {
    stop();
    process.exit(0);
  });
  process.on('SIGINT', () => {
    stop();
    process.exit(0);
  });

  let ready = false;
  listen(port, (req, res) => {
    if (!ready) {
      res.writeHead(503, { 'Content-Type': 'text/plain; charset=UTF-8', 'Retry-After': '3' });
      res.end('Starting Laravel.\n');
      return;
    }
    proxyTo(phpPort, req, res);
  });
  console.log(`Node listening on ${port}`);

  try {
    await waitForPhp(phpPort);
  } catch (error) {
    console.error(error.message);
    return;
  }

  await artisan(php, ['migrate', '--force'], env);
  for (const seeder of ['CitySeeder', 'SettingSeeder', 'GuideSeeder']) {
    await artisan(php, ['db:seed', `--class=${seeder}`, '--force'], env);
  }
  if (userCount(php, env) === 0) {
    await artisan(php, ['db:seed', '--class=UserSeeder', '--force'], env);
  }
  ready = true;
  console.log('Laravel is ready');
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
