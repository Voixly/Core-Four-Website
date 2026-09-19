<?php

use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\GuideController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Public\ChatWidgetController;
use App\Http\Controllers\Public\LeadFormController;
use App\Http\Controllers\Public\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [PageController::class, 'sitemap']);
Route::get('/robots.txt', [PageController::class, 'robots']);

Route::permanentRedirect('/home/', '/');
Route::permanentRedirect('/home', '/');
Route::permanentRedirect('/about-us/', '/about-core-four-roofing/');
Route::permanentRedirect('/about-us', '/about-core-four-roofing/');
Route::permanentRedirect('/about/', '/about-core-four-roofing/');
Route::permanentRedirect('/about', '/about-core-four-roofing/');
Route::permanentRedirect('/areas-we-serve/', '/service-areas/');
Route::permanentRedirect('/areas-we-serve', '/service-areas/');
Route::permanentRedirect('/emergency-roof-repair/', '/storm-emergency/');
Route::permanentRedirect('/emergency-roof-repair', '/storm-emergency/');
Route::permanentRedirect('/emergency-roofing/', '/storm-emergency/');
Route::permanentRedirect('/emergency-roofing', '/storm-emergency/');
// Short material URLs the old build used, folded back onto the live structure.
Route::permanentRedirect('/asphalt-shingles/', '/residential-roofing/asphalt-shingles/');
Route::permanentRedirect('/metal-roofing/', '/residential-roofing/metal-roofs/');
Route::permanentRedirect('/synthetic-roofing/', '/residential-roofing/synthetic-roofs/');
Route::permanentRedirect('/stone-coated-steel/', '/residential-roofing/stone-coated-steel/');
Route::permanentRedirect('/tpo-roofing/', '/commercial-roofing/roof-replacement-installation/');
Route::permanentRedirect('/epdm-roofing/', '/commercial-roofing/roof-replacement-installation/');
Route::permanentRedirect('/modified-bitumen/', '/commercial-roofing/roof-replacement-installation/');
Route::permanentRedirect('/built-up-roofing/', '/commercial-roofing/roof-replacement-installation/');

Route::get('/', [PageController::class, 'home'])->name('home');

// Pages rebuilt from the live site's own structure.
$structured = [
    'residential-roofing' => 'residential',
    'commercial-roofing' => 'commercial',
    'insurance-claims' => 'insurance',
    'financing' => 'financing',
    'storm-emergency' => 'emergency',
    'about-core-four-roofing' => 'about',
    'service-areas' => 'service-areas',
    'contact-core-four-roofing' => 'contact',
    'blog' => 'blog',
    'residential-roofing/asphalt-shingles' => 'asphalt-shingles',
    'residential-roofing/metal-roofs' => 'metal-roofs',
    'residential-roofing/stone-coated-steel' => 'stone-coated-steel',
    'residential-roofing/synthetic-roofs' => 'synthetic-roofs',
    'residential-roofing/roof-installation' => 'roof-installation',
    'residential-roofing/roof-inspections' => 'roof-inspections',
    'residential-roofing/roof-repair' => 'roof-repair',
    'commercial-roofing/coatings-restoration' => 'coatings-restoration',
    'commercial-roofing/inspections-condition-reports' => 'inspections-condition-reports',
    'commercial-roofing/repair-preventative-maintenance' => 'repair-preventative-maintenance',
    'commercial-roofing/roof-replacement-installation' => 'roof-replacement-installation',
];

foreach ($structured as $path => $name) {
    Route::get('/'.$path.'/', [PageController::class, 'page'])
        ->defaults('slug', str_replace('/', '--', $path))
        ->name($name);
}
Route::get('/thank-you/', [PageController::class, 'thanks'])->name('thanks');
Route::get('/privacy-policy/', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms/', [PageController::class, 'terms'])->name('terms');
Route::get('/guides/', [PageController::class, 'guides'])->name('guides');
Route::get('/guides/{slug}', [PageController::class, 'guide'])->name('guides.show');

Route::get('/residential-roofing-in-tx/', [PageController::class, 'city'])->defaults('slug', 'tx');
Route::get('/commercial-roofing-in-tx/', [PageController::class, 'city'])->defaults('slug', 'tx');
Route::get('/residential-roofing-in-{slug}-tx/', [PageController::class, 'city'])->name('city.residential');
Route::get('/commercial-roofing-in-{slug}-tx/', [PageController::class, 'city'])->name('city.commercial');

Route::post('/leads', [LeadFormController::class, 'store'])->middleware('throttle:8,1')->name('leads.store');
Route::post('/guides/{slug}/download', [LeadFormController::class, 'download'])->middleware('throttle:6,1')->name('guides.download');

Route::prefix('chat')->middleware('throttle:40,1')->group(function () {
    Route::post('/start', [ChatWidgetController::class, 'start'])->name('chat.start');
    Route::get('/poll', [ChatWidgetController::class, 'poll'])->name('chat.poll');
    Route::post('/send', [ChatWidgetController::class, 'send'])->name('chat.send');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:8,1');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:agency,owner,staff'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::patch('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::post('/leads/{lead}/notes', [LeadController::class, 'note'])->name('leads.note');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/{conversation}/poll', [ChatController::class, 'poll'])->name('chat.poll');
    Route::post('/chat/{conversation}/reply', [ChatController::class, 'reply'])->name('chat.reply');
    Route::post('/chat/{conversation}/close', [ChatController::class, 'close'])->name('chat.close');
    Route::post('/chat/{conversation}/convert', [ChatController::class, 'convert'])->name('chat.convert');

    Route::middleware('role:agency,owner')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{slug}', [ReportController::class, 'show'])->name('reports.show');

        Route::get('/email', [EmailController::class, 'index'])->name('email.index');
        Route::get('/email/steps/{step}', [EmailController::class, 'edit'])->name('email.edit');
        Route::patch('/email/steps/{step}', [EmailController::class, 'update'])->name('email.update');
        Route::get('/email/steps/{step}/preview', [EmailController::class, 'preview'])->name('email.preview');
        Route::post('/email/steps/{step}/test', [EmailController::class, 'test'])->name('email.test');

        Route::get('/guides', [GuideController::class, 'index'])->name('guides.index');
        Route::post('/guides', [GuideController::class, 'store'])->name('guides.store');
        Route::post('/guides/{guide}/toggle', [GuideController::class, 'toggle'])->name('guides.toggle');
    });

    Route::middleware('role:agency')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');

        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
