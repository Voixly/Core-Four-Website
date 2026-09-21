/* The nav turns into a slide-out drawer below 1100px. */
(() => {
  const nav = document.getElementById("site-nav");
  const toggle = document.querySelector(".menu-toggle");
  const scrim = document.querySelector(".nav-scrim");
  if (!nav || !toggle || !scrim) return;

  const isOpen = () => nav.classList.contains("is-open");

  // Only what is actually reachable: collapsed submenu links are clipped to
  // zero height and must not swallow a tab stop.
  const stops = () =>
    [...nav.querySelectorAll("a[href], button")].filter(
      (el) => el.getClientRects().length > 0
    );

  let opener = null;

  const open = () => {
    opener = document.activeElement;
    document.documentElement.classList.add("nav-open");
    nav.classList.add("is-open");
    scrim.classList.add("is-open");
    toggle.setAttribute("aria-expanded", "true");
    nav.querySelector(".nav-close").focus();
  };

  const close = (returnFocus = true) => {
    document.documentElement.classList.remove("nav-open");
    nav.classList.remove("is-open");
    scrim.classList.remove("is-open");
    toggle.setAttribute("aria-expanded", "false");
    if (returnFocus && opener) opener.focus();
    opener = null;
  };

  toggle.addEventListener("click", () => (isOpen() ? close() : open()));
  document
    .querySelectorAll("[data-nav-close]")
    .forEach((el) => el.addEventListener("click", () => close()));

  // Following a link closes the drawer; the destination takes the focus.
  nav.addEventListener("click", (e) => {
    if (e.target.closest("a[href]")) close(false);
  });

  nav.querySelectorAll(".sub-toggle").forEach((button) => {
    button.addEventListener("click", () => {
      const group = button.closest(".has-sub");
      button.setAttribute(
        "aria-expanded",
        String(group.classList.toggle("is-expanded"))
      );
    });
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && isOpen()) close();
  });

  // Keep tabbing inside the drawer while it covers the page.
  nav.addEventListener("keydown", (e) => {
    if (e.key !== "Tab" || !isOpen()) return;
    const items = stops();
    if (!items.length) return;
    const edge = e.shiftKey ? items[0] : items[items.length - 1];
    if (document.activeElement !== edge) return;
    e.preventDefault();
    (e.shiftKey ? items[items.length - 1] : items[0]).focus();
  });

  // Widening past the breakpoint puts the nav back in the header, where an
  // open drawer and a locked page would both be wrong.
  window.matchMedia("(min-width: 1101px)").addEventListener("change", (e) => {
    if (e.matches && isOpen()) close(false);
  });
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
