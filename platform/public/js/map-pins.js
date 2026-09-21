/* Frame Elfsight maps on the Core Four pins. The widget is saved with a
   San Angelo / Waco center, so a phone-width view leaves the pins off-screen. */
(() => {
  const pins = [
    { lat: 30.7235263, lng: -95.5507771 },
    { lat: 29.8832749, lng: -97.9413941 },
    { lat: 32.9394524, lng: -97.078668 },
    { lat: 30.0197861, lng: -95.5964528 },
  ];

  const seen = new Set();

  const bounds = () => {
    const box = new google.maps.LatLngBounds();
    pins.forEach((pin) => box.extend(pin));
    return box;
  };

  const gutter = (map) => {
    const card = map.getDiv()?.closest(".map-section")?.querySelector(".map-card");
    if (card && getComputedStyle(card).position === "absolute") {
      return {
        top: 56,
        right: 40,
        bottom: 56,
        left: Math.round(card.getBoundingClientRect().width + 48),
      };
    }
    return { top: 48, right: 36, bottom: 48, left: 36 };
  };

  const onPins = (map) => {
    const center = map.getCenter?.();
    if (!center) return false;
    return center.lng() > -98.4 && center.lng() < -94.8;
  };

  const frame = (map) => {
    if (!map || map.__c4user || map.__c4framing || typeof map.fitBounds !== "function") return;
    if (map.__c4ready && onPins(map)) return;
    map.__c4framing = true;
    map.fitBounds(bounds(), gutter(map));
    map.__c4framing = false;
    map.__c4ready = true;
  };

  const watch = (map) => {
    if (!map || seen.has(map)) return;
    seen.add(map);
    const go = () => frame(map);
    google.maps.event.addListenerOnce(map, "idle", go);
    google.maps.event.addListener(map, "tilesloaded", go);
    google.maps.event.addListener(map, "dragstart", () => {
      map.__c4user = true;
    });
    [300, 800, 1600, 3200].forEach((ms) => setTimeout(go, ms));
    window.addEventListener("resize", () => {
      clearTimeout(map.__c4pins);
      map.__c4pins = setTimeout(go, 200);
    });
  };

  const patch = () => {
    const proto = window.google?.maps?.Map?.prototype;
    if (!proto || proto.__c4pins) return Boolean(proto?.__c4pins);
    const origSetCenter = proto.setCenter;
    proto.setCenter = function (...args) {
      const result = origSetCenter.apply(this, args);
      if (!this.__c4framing) watch(this);
      return result;
    };
    proto.__c4pins = true;
    return true;
  };

  const tick = setInterval(() => {
    if (patch()) clearInterval(tick);
  }, 40);
})();
