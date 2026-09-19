(() => {
  const header = document.querySelector(".site-header");
  if (!header) return;
  const first = document.querySelector("main > :first-child");
  const hasHero = first && first.classList.contains("hero");
  const paint = () => {
    header.classList.toggle("is-scrolled", window.scrollY > 24);
    if (!hasHero) header.classList.add("solid");
  };
  paint();
  window.addEventListener("scroll", paint, { passive: true });
})();

document.querySelectorAll("[data-ba]").forEach((root) => {
  const range = root.querySelector(".ba-range");
  const before = root.querySelector(".ba-before-wrap");
  const handle = root.querySelector(".ba-handle");
  if (!range || !before || !handle) return;

  const size = () => {
    root.style.setProperty("--ba-full", root.clientWidth + "px");
  };

  const paint = (value) => {
    const pct = Number(value);
    before.style.width = pct + "%";
    handle.style.left = pct + "%";
  };

  size();
  paint(range.value);
  range.addEventListener("input", () => paint(range.value));
  window.addEventListener("resize", size);
});

document.querySelectorAll("[data-review-slider]").forEach((root) => {
  const slides = [...root.querySelectorAll(".featured-review")];
  const dotsWrap = root.querySelector(".review-dots");
  if (!slides.length) return;
  let index = 0;

  slides.forEach((_, i) => {
    const dot = document.createElement("button");
    dot.type = "button";
    dot.addEventListener("click", () => go(i));
    dotsWrap?.appendChild(dot);
  });

  const dots = [...(dotsWrap?.querySelectorAll("button") || [])];

  const go = (next) => {
    index = (next + slides.length) % slides.length;
    slides.forEach((slide, i) => slide.classList.toggle("is-active", i === index));
    dots.forEach((dot, i) => dot.classList.toggle("is-active", i === index));
  };

  root.querySelector(".review-nav.prev")?.addEventListener("click", () => go(index - 1));
  root.querySelector(".review-nav.next")?.addEventListener("click", () => go(index + 1));
  go(0);
});
