#!/usr/bin/env python3
"""Build static preview pages that mirror the live Core Four structure."""
from __future__ import annotations

import json
import re
from pathlib import Path

ROOT = Path("/Users/coltonrucker/Core Four Report/platform")
PAGES = ROOT / "resources/data/pages"
OUT = ROOT / "public/preview"
WIDTHS = {
    "one": 12, "one-second": 6, "one-third": 4, "two-third": 8,
    "one-fourth": 3, "three-fourth": 9, "one-fifth": 2, "two-fifth": 5,
    "three-fifth": 7, "four-fifth": 10, "one-sixth": 2, "five-sixth": 10,
}
ICONS = {
    "afford": "fas fa-wallet", "efficien": "fas fa-sync-alt",
    "integr": "fas fa-handshake", "quality": "fas fa-award",
    "hub": "fas fa-map-pin", "storm": "fas fa-clock",
    "office": "fas fa-phone", "phone": "fas fa-phone",
}
FAQ = [
    ("Roof Consultation", "We provide a comprehensive roof inspection to evaluate damage, assess lifespan, and determine the most cost-effective solution for your commercial or residential property."),
    ("Professional Installation", "From TPO and EPDM for commercial buildings to premium asphalt shingles for homes, our licensed crews install your new roof efficiently—often in just 1-3 days."),
    ("Quality Control", "After installation, we perform a rigorous quality inspection. We leave your property spotless and ensure you have all warranty documentation for complete peace of mind."),
]


LEAD_FORM = """<form class="lead-form" onsubmit="return false">
    <p class="form-note">"<span>*</span>" indicates required fields</p>
    <fieldset class="field-group">
        <legend>Name</legend>
        <div class="field-row">
            <label class="sub"><input name="name" required><span class="sub-label">First</span></label>
            <label class="sub"><input name="last_name"><span class="sub-label">Last</span></label>
        </div>
    </fieldset>
    <div class="field-row">
        <label>Email <span>*</span><input name="email" type="email" required></label>
        <label>Phone <span>*</span><input name="phone" type="tel" required></label>
    </div>
    <div class="field-row">
        <label>Select a Service <span>*</span>
            <select name="need">
                <option value="">Please Select a Service</option>
                <option>Commercial Roofing</option>
                <option>Residential Roofing</option>
                <option>Premium Roofing</option>
                <option>Exterior Services</option>
            </select>
        </label>
        <fieldset class="field-group">
            <legend>Address</legend>
            <label class="sub"><input name="zip"><span class="sub-label">ZIP Code</span></label>
        </fieldset>
    </div>
    <label>Comments<textarea name="message" maxlength="600" placeholder="Please let us know what's on your mind. Have a question for us? Ask away."></textarea></label>
    <button class="btn" type="submit">Submit <i class="fas fa-arrow-right"></i></button>
</form>"""


def href(url: str) -> str:
    if not url:
        return "#"
    if url.startswith(("tel:", "mailto:", "#")):
        return url
    url = re.sub(r"^https?://(www\.)?corefourroofing\.com", "", url)
    if url in ("", "/"):
        return "/preview/index.html"
    slug = url.strip("/").replace("/", "--")
    return f"/preview/{slug}.html"


def restore_icons(html: str) -> str:
    queue = ["fas fa-map-pin", "fas fa-clock", "fas fa-phone"]
    i = 0

    def repl(_m):
        nonlocal i
        icon = queue[i] if i < len(queue) else "fas fa-check"
        i += 1
        return f'<i class="{icon}" aria-hidden="true"></i>'

    return re.sub(r"<i(?:\s+class=\"\")?></i>", repl, html or "")


def icon_for(title: str) -> str:
    t = title.lower()
    for key, icon in ICONS.items():
        if key in t:
            return icon
    return "fas fa-check"


def ba(before, after, balt="Before", aalt="After"):
    return f"""<div class="ba" data-ba>
    <img class="ba-after" src="{after}" alt="{aalt}">
    <div class="ba-before-wrap"><img class="ba-before" src="{before}" alt="{balt}"></div>
    <span class="ba-tag before">Before</span>
    <span class="ba-tag after">After</span>
    <div class="ba-handle"></div>
    <input class="ba-range" type="range" min="8" max="92" value="50" aria-label="Compare before and after">
</div>"""


def gallery_columns(item: dict) -> int:
    """Galleries flow into masonry columns. The column count is the
    container width over the narrowest image, which is how the live grid lands
    on three columns for photo walls and two for the hero badge block."""
    width = (item.get("box") or {}).get("w")
    widths = [im["w"] for im in item.get("images", []) if im.get("w")]
    if not width or not widths:
        return 1
    return max(1, min(4, round(width / min(widths))))


def section_has_content(section: dict) -> bool:
    def walk(node):
        item = node.get("item") or {}
        if item.get("type") and (item.get("html") or item.get("text") or item.get("images") or item.get("img")):
            return True
        return any(walk(c) for c in (node.get("wraps") or []) + (node.get("columns") or []))

    return any(walk(w) for w in section.get("wraps") or [])


def blog_posts(html: str) -> str:
    """The blog listing arrives as one flat run of h3/p/"Read More" per post with
    no wrapper, so rebuild the per-post boxes the live three-up grid needs."""
    chunks = re.split(r"(?=<h3>)", html)
    chunks = [c for c in chunks if c.strip().startswith("<h3>")]
    if len(chunks) < 3:
        return ""
    return "".join(f'<article class="blk-post">{c}</article>' for c in chunks)


def type_style(item: dict) -> str:
    """Carry the colour/weight/leading measured off the live element. Font size is
    left to the responsive scale so it still steps down on small screens."""
    parts = []
    if item.get("color"):
        parts.append(f"color:{item['color']}")
    if item.get("weight"):
        parts.append(f"font-weight:{item['weight']}")
    if item.get("lh") and item.get("size"):
        try:
            ratio = float(str(item["lh"]).replace("px", "")) / float(item["size"])
            parts.append(f"line-height:{ratio:.3f}")
        except (TypeError, ValueError):
            pass
    if item.get("align") and item["align"] not in ("start", "left"):
        parts.append(f"text-align:{item['align']}")
    if item.get("transform") and item["transform"] != "none":
        parts.append(f"text-transform:{item['transform']}")
    return f' style="{";".join(parts)}"' if parts else ""


def render_item(item: dict) -> str:
    t = item.get("type")
    if t == "heading":
        lvl = min(6, max(1, item.get("level") or 2))
        light = " light-text" if item.get("light") else ""
        return f'<h{lvl} class="blk-h blk-h{lvl}{light}"{type_style(item)}>{item.get("html","")}</h{lvl}>'
    if t == "text":
        html = restore_icons(item.get("html") or "")
        if "Roofing Partners" in html or "Associate Memberships" in html:
            return "<!-- logos injected at section -->"
        light = " light-text" if item.get("light") else ""
        return f'<div class="blk-text{light}"{type_style(item)}>{html}</div>'
    if t == "image" and item.get("img"):
        img = item["img"]
        return f'<img class="blk-img" src="{img.get("src","")}" alt="{img.get("alt","")}" loading="lazy">'
    if t == "button":
        return f'<a class="btn" href="{href(item.get("href","#"))}">{item.get("text","")} <i class="fas fa-arrow-right"></i></a>'
    if t == "before_after" and item.get("before") and item.get("after"):
        return ba(item["before"]["src"], item["after"]["src"], item["before"].get("alt") or "Before", item["after"].get("alt") or "After")
    if t == "elfsight":
        return f'<div class="elfsight-app-{item.get("widget","")}" data-elfsight-app-lazy></div>'
    if t == "faq":
        items = item.get("items") or [{"q": q, "a": a} for q, a in FAQ]
        bits = ['<div class="faq blk-faq">']
        for i, q in enumerate(items):
            open_ = " open" if i == 0 else ""
            bits.append(f'<details{open_}><summary><span class="step-num">{i+1}</span> {q["q"]}</summary><div class="faq-body">{q["a"]}</div></details>')
        bits.append("</div>")
        return "".join(bits)
    if t == "icon_box_2":
        html = item.get("html") or ""
        m = re.search(r"<h3[^>]*>(.*?)</h3>", html, re.S)
        title = re.sub("<[^>]+>", "", m.group(1)) if m else ""
        body = re.sub(r"<i(?:\s+class=\"\")?></i>", "", html)
        return f'<div class="icon-box"><div class="icon-wrapper"><i class="{icon_for(title)}" aria-hidden="true"></i></div>{body}</div>'
    if t == "counter":
        return f'<div class="counter-card">{item.get("html","")}</div>'
    if t == "form":
        return LEAD_FORM
    if t == "gallery":
        images = item.get("images", [])
        bits = []
        for im in images:
            ratio = ""
            if im.get("w") and im.get("h"):
                ratio = f' style="aspect-ratio:{im["w"]}/{im["h"]}"'
            bits.append(f'<img src="{im.get("src","")}" alt="{im.get("alt","")}"{ratio} loading="lazy">')
        return f'<div class="blk-gallery" style="--cols:{gallery_columns(item)}">{"".join(bits)}</div>'
    if t in ("html",) or item.get("html"):
        html = restore_icons(item.get("html") or "")
        posts = blog_posts(html)
        if posts:
            return f'<div class="blk-blog">{posts}</div>'
        return f'<div class="blk-raw">{html}</div>'
    return ""


def span_vars(node: dict) -> str:
    classes = node.get("classes") or []
    declared = WIDTHS.get(node.get("width") or "one", 12)
    span = node.get("span") or declared
    parts = [f"--span:{span}"]
    # Source grids (comparison tables) override the declared column class, so the
    # measured span wins. The breakpoint classes then inherit that same override
    # instead of snapping back to full width — except on mobile, which stacks.
    overridden = span != declared
    for bp in ("laptop", "tablet", "mobile"):
        prefix = bp + "-"
        value = None
        for c in classes:
            if c.startswith(prefix) and c[len(prefix):] in WIDTHS:
                value = WIDTHS[c[len(prefix):]]
        if value is None:
            continue
        if overridden and value == declared and bp != "mobile":
            value = span
        parts.append(f"--span-{bp}:{value}")
    return ";".join(parts)


def media_class(node: dict, parent: dict | None) -> str:
    """Card images sit flush at the card bottom: full-width ones round the bottom
    corners, inset ones round the top."""
    if (node.get("item") or {}).get("type") != "image" or not parent:
        return ""
    card = parent.get("box") or {}
    box = node.get("box") or {}
    if not (card.get("bg") or card.get("bgImage") or card.get("radius")):
        return ""
    if not all(k in card for k in ("y", "h", "w")) or not all(k in box for k in ("y", "h", "w")):
        return ""
    if abs((box["y"] + box["h"]) - (card["y"] + card["h"])) > 4:
        return ""
    return " blk-media-bleed" if box["w"] >= card["w"] - 4 else " blk-media-inset"


def row_columns(node: dict) -> str:
    """Comparison tables declare every cell full-width and rely on a grid
    the extractor didn't capture. When the measured boxes show the children
    actually sit on one row, rebuild that row from their real widths."""
    kids = (node.get("columns") or []) + (node.get("wraps") or [])
    box = node.get("box") or {}
    if len(kids) < 2 or not box.get("w"):
        return ""
    boxes = [k.get("box") or {} for k in kids]
    if not all(b.get("w") for b in boxes):
        return ""
    # Side by side iff the children's widths add up to roughly one container
    # width; stacked children would each be the full width and overshoot badly.
    total = sum(b["w"] for b in boxes)
    if not (box["w"] * 0.8 <= total <= box["w"] * 1.15):
        return ""
    declared = sum(k.get("span") or WIDTHS.get(k.get("width") or "one", 12) for k in kids)
    if declared == 12:
        return ""
    return " ".join(f"{b['w']}fr" for b in boxes)


def render_node(node: dict, parent: dict | None = None) -> str:
    kind = node.get("kind", "column")
    cls = "blk-wrap" if kind == "wrap" else "blk-col"
    box = node.get("box") or {}
    if box.get("bg") or box.get("bgImage") or box.get("radius"):
        cls += " blk-surface"
    if "light-text" in (node.get("classes") or []):
        cls += " light-text"
    cls += media_class(node, parent)
    style = span_vars(node)
    cols = row_columns(node)
    if cols:
        cls += " blk-row"
        style += f";grid-template-columns:{cols}"
    if box.get("bg"):
        style += f";background-color:{box['bg']}"
    if box.get("bgImage"):
        style += f";background-image:url('{box['bgImage']}');background-size:cover;background-position:center"
    if box.get("radius"):
        style += f";border-radius:{box['radius']}px"
    if box.get("padding") and sum(box["padding"]) > 0:
        style += ";padding:" + "px ".join(str(p) for p in box["padding"]) + "px"
    html = [f'<div class="{cls}" style="{style}">']
    for child in node.get("columns") or []:
        html.append(render_node(child, node))
    for child in node.get("wraps") or []:
        html.append(render_node(child, node))
    if node.get("item"):
        html.append(render_item(node["item"]))
    html.append("</div>")
    return "".join(html)


def render_page(data: dict) -> str:
    parts = []
    for i, section in enumerate(data.get("sections") or []):
        widgets = json.dumps(section)
        # The hand-built coverage block only carries the home/service-areas copy;
        # other pages put their own heading above the same map and must render
        # normally so that copy survives.
        if "058d3df7-5422-475a-ab17-f5e14c220034" in widgets and "Protecting Texas" in widgets:
            parts.append(MAP)
            continue
        if i > 0 and "267ccdad-1b32-4c62-9394-105914e96f0f" in widgets and "mfn-global-section" in " ".join(section.get("classes") or []):
            parts.append(REVIEWS)
            continue
        if "71bb658f-56ca-4f80-8bfc-064bde22918c" in widgets:
            parts.append(INSTAGRAM)
            continue
        is_hero = i == 0 and section.get("bgImage")
        classes = ["blk-section"]
        if is_hero:
            classes.append("blk-hero")
        section_classes = section.get("classes") or []
        if "full-width" in section_classes or "full-width-ex-mobile" in section_classes:
            classes.append("blk-section--full")
        if "full-screen" in section_classes:
            classes.append("blk-section--screen")
        if "dark" in section_classes:
            classes.append("blk-section--dark")
        # A leading section with a background but no content exists only to sit
        # behind the fixed header, so it has to carry the header's height.
        if i == 0 and section.get("bg") and not section_has_content(section):
            classes.append("blk-section--spacer")
        style = []
        if section.get("bg"):
            style.append(f"background-color:{section['bg']}")
        if section.get("bgImage"):
            style.append(f"background-image:url('{section['bgImage']}')")
            style.append(f"background-size:{section.get('bgSize') or 'cover'}")
            style.append(f"background-position:{section.get('bgPos') or 'center'}")
        if section.get("padding"):
            style.append("padding:" + "px ".join(str(p) for p in section["padding"]) + "px")
        overlay = (section.get("overlay") or {}).get("gradient")
        opacity = (section.get("overlay") or {}).get("opacity") or "1"
        body = []
        if overlay:
            body.append(f'<div class="blk-hero-scrim" style="background:{overlay};opacity:{opacity}"></div>')
        elif is_hero:
            body.append('<div class="blk-hero-scrim"></div>')
        body.append('<div class="blk-wrapper">')
        dumped = json.dumps(section)
        if "Roofing Partners" in dumped:
            body.append('<div class="blk-wrap" style="--span:12"><h2 class="logos-heading">Reliable Commercial &amp; Residential Roof Repair, Replacement &amp; Maintenance</h2>' + LOGOS + "</div>")
        else:
            for wrap in section.get("wraps") or []:
                body.append(render_node(wrap))
        body.append("</div>")
        parts.append(f'<section class="{" ".join(classes)}" style="{";".join(style)}">{"".join(body)}</section>')
    return "".join(parts)


CHROME_HEAD = """<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{title}</title>
    <link rel="icon" href="/images/favicon-live.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css?family=Reddit+Sans:500,500italic,600,600italic,700,700italic&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.typekit.net/wci4ksj.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="/css/site.css">
    <link rel="stylesheet" href="/css/blocks.css">
    <script src="https://elfsightcdn.com/platform.js" async></script>
</head>
<body>
<header class="site-header{solid}">
    <div class="header-inner">
        <a class="logo" href="/preview/index.html">
            <img class="logo-light" src="/images/logo-live.svg" alt="Core Four Roofing">
            <img class="logo-dark" src="/images/logo-color.svg" alt="Core Four Roofing">
        </a>
        <button class="menu-toggle" type="button" onclick="document.querySelector('.nav').classList.toggle('open')" aria-label="Menu"><i class="fas fa-bars"></i></button>
        <nav class="nav">
            <div class="has-sub">
                <a href="/preview/commercial-roofing.html">Commercial</a>
                <div class="sub">
                    <a href="/preview/commercial-roofing--roof-replacement-installation.html">Roof Replacement &amp; Installation</a>
                    <a href="/preview/commercial-roofing--repair-preventative-maintenance.html">Repair &amp; Preventative Maintenance</a>
                    <a href="/preview/commercial-roofing--coatings-restoration.html">Coatings &amp; Restoration</a>
                    <a href="/preview/commercial-roofing--inspections-condition-reports.html">Inspections &amp; Condition Reports</a>
                </div>
            </div>
            <div class="has-sub">
                <a href="/preview/residential-roofing.html">Residential</a>
                <div class="sub">
                    <a href="/preview/residential-roofing--asphalt-shingles.html">Asphalt Shingles</a>
                    <a href="/preview/residential-roofing--metal-roofs.html">Metal Roofs</a>
                    <a href="/preview/residential-roofing--synthetic-roofs.html">Synthetic Roofs</a>
                    <a href="/preview/residential-roofing--stone-coated-steel.html">Stone-Coated Steel</a>
                </div>
            </div>
            <a href="/preview/storm-emergency.html">Storm &amp; Emergency</a>
            <a href="/preview/about-core-four-roofing.html">About</a>
            <a class="nav-cta" href="/preview/contact-core-four-roofing.html">Get a Free Inspection</a>
        </nav>
    </div>
</header>
<main>
"""

CHROME_FOOT = """
</main>
<footer class="site-footer">
    <div class="wrap">
        <div class="footer-grid">
            <div class="footer-col">
                <h6>Residential Services</h6>
                <ul class="footer-links">
                    <li><a href="/preview/residential-roofing--stone-coated-steel.html">Stone-Coated Steel</a></li>
                    <li><a href="/preview/residential-roofing--synthetic-roofs.html">Synthetic Roofs</a></li>
                    <li><a href="/preview/residential-roofing--metal-roofs.html">Metal Roofs</a></li>
                    <li><a href="/preview/residential-roofing--asphalt-shingles.html">Asphalt Shingles</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h6>Commercial Services</h6>
                <ul class="footer-links">
                    <li><a href="/preview/commercial-roofing--inspections-condition-reports.html">Inspections &amp; Condition Reports</a></li>
                    <li><a href="/preview/commercial-roofing--coatings-restoration.html">Coatings &amp; Restoration</a></li>
                    <li><a href="/preview/commercial-roofing--repair-preventative-maintenance.html">Repair &amp; Preventative Maintenance</a></li>
                    <li><a href="/preview/commercial-roofing--roof-replacement-installation.html">Roof Replacement &amp; Installation</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h6>Company</h6>
                <ul class="footer-links">
                    <li><a href="/preview/blog.html">Blog</a></li>
                    <li><a href="/preview/service-areas.html">Services Areas</a></li>
                    <li><a href="/preview/contact-core-four-roofing.html">Contact Core Four Roofing</a></li>
                    <li><a href="/preview/about-core-four-roofing.html">About Core Four Roofing</a></li>
                    <li><a href="/preview/financing.html">Financing</a></li>
                    <li><a href="/preview/insurance-claims.html">Insurance</a></li>
                </ul>
                <div class="socials">
                    <a href="https://www.facebook.com/corefourroofing/" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/corefourroofing/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.linkedin.com/company/core-four-roofing/" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-cta">
                <h4>Protect your property. Don't wait.</h4>
                <p>Get a free, no-obligation inspection from the #1 local roofing experts in Texas.</p>
                <a class="btn" href="tel:+12815410027">Call Today <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="footer-bottom">
            <div>© Copyright 2026 Core Four Roofing &amp; Construction | Website by <a href="https://voixly.com/" target="_blank" rel="noopener">Voixly</a></div>
            <div>22955 State Highway 249 Suite 26<br>Tomball, TX 77375</div>
        </div>
    </div>
</footer>
<script src="/js/site.js"></script>
</body>
</html>
"""

LOGOS = """
<div class="c4-logo-section">
    <div class="c4-section-label"><span class="pill">Certified</span><span class="title">Roofing Partners</span></div>
    <div class="c4-track-wrap"><div class="c4-track c4-track--partners">
        <div class="c4-logo"><img src="/images/partners/gaf.webp" alt="GAF"></div>
        <div class="c4-logo"><img src="/images/partners/gaf-comm.webp" alt="GAF Commercial"></div>
        <div class="c4-logo"><img src="/images/partners/certainteed.webp" alt="CertainTeed"></div>
        <div class="c4-logo"><img src="/images/partners/iko.webp" alt="IKO"></div>
        <div class="c4-logo"><img src="/images/partners/malarkey.webp" alt="Malarkey"></div>
        <div class="c4-logo"><img src="/images/partners/decra.webp" alt="DECRA"></div>
        <div class="c4-logo"><img src="/images/partners/elevate.webp" alt="Elevate"></div>
        <div class="c4-logo"><img src="/images/partners/gaf.webp" alt="GAF"></div>
        <div class="c4-logo"><img src="/images/partners/gaf-comm.webp" alt="GAF Commercial"></div>
        <div class="c4-logo"><img src="/images/partners/certainteed.webp" alt="CertainTeed"></div>
        <div class="c4-logo"><img src="/images/partners/iko.webp" alt="IKO"></div>
        <div class="c4-logo"><img src="/images/partners/malarkey.webp" alt="Malarkey"></div>
        <div class="c4-logo"><img src="/images/partners/decra.webp" alt="DECRA"></div>
        <div class="c4-logo"><img src="/images/partners/elevate.webp" alt="Elevate"></div>
    </div></div>
</div>
<div class="c4-logo-section">
    <div class="c4-section-label"><span class="pill">Members</span><span class="title">Associate Memberships &amp; Awards</span></div>
    <div class="c4-track-wrap"><div class="c4-track c4-track--members">
        <div class="c4-logo"><img src="/images/partners/bbb.webp" alt="BBB"></div>
        <div class="c4-logo"><img src="/images/partners/angi.webp" alt="Angi"></div>
        <div class="c4-logo"><img src="/images/partners/homeadvisor.webp" alt="HomeAdvisor"></div>
        <div class="c4-logo"><img src="/images/partners/nrca.webp" alt="NRCA"></div>
        <div class="c4-logo"><img src="/images/partners/harca.webp" alt="HARCA"></div>
        <div class="c4-logo"><img src="/images/partners/rcat.webp" alt="RCAT"></div>
        <div class="c4-logo"><img src="/images/partners/haa.webp" alt="HAA"></div>
        <div class="c4-logo"><img src="/images/partners/taa.webp" alt="TAA"></div>
        <div class="c4-logo"><img src="/images/partners/thla.webp" alt="THLA"></div>
        <div class="c4-logo"><img src="/images/partners/cai.webp" alt="CAI"></div>
        <div class="c4-logo"><img src="/images/partners/ifma.webp" alt="IFMA"></div>
        <div class="c4-logo"><img src="/images/partners/bbb.webp" alt="BBB"></div>
        <div class="c4-logo"><img src="/images/partners/angi.webp" alt="Angi"></div>
        <div class="c4-logo"><img src="/images/partners/homeadvisor.webp" alt="HomeAdvisor"></div>
        <div class="c4-logo"><img src="/images/partners/nrca.webp" alt="NRCA"></div>
        <div class="c4-logo"><img src="/images/partners/harca.webp" alt="HARCA"></div>
        <div class="c4-logo"><img src="/images/partners/rcat.webp" alt="RCAT"></div>
        <div class="c4-logo"><img src="/images/partners/haa.webp" alt="HAA"></div>
        <div class="c4-logo"><img src="/images/partners/taa.webp" alt="TAA"></div>
        <div class="c4-logo"><img src="/images/partners/thla.webp" alt="THLA"></div>
        <div class="c4-logo"><img src="/images/partners/cai.webp" alt="CAI"></div>
        <div class="c4-logo"><img src="/images/partners/ifma.webp" alt="IFMA"></div>
    </div></div>
</div>
"""

MAP = """
<section class="section section--flush map-section">
    <div class="map-embed"><div class="elfsight-app-058d3df7-5422-475a-ab17-f5e14c220034" data-elfsight-app-lazy></div></div>
    <div class="map-card">
        <h4>Protecting Texas,<br>One Roof at a Time</h4>
        <p>Core Four Roofing Service Coverage</p>
        <ul class="coverage-list">
            <li><i class="fas fa-map-pin" aria-hidden="true"></i><h6>Service Hubs</h6><p>Austin, Dallas, Houston</p></li>
            <li><i class="fas fa-clock" aria-hidden="true"></i><h6>Storm Response</h6><p>24/7 Emergency Tarping &amp; Repair</p></li>
            <li><i class="fas fa-phone" aria-hidden="true"></i><h6>Local Office</h6><p>(281) 541-0027</p></li>
        </ul>
    </div>
</section>
"""

REVIEWS = """
<section class="section section--reviews">
    <div class="wrap">
        <div class="reviews-badge"><div class="elfsight-app-267ccdad-1b32-4c62-9394-105914e96f0f" data-elfsight-app-lazy></div></div>
        <div class="review-slider" data-review-slider>
            <button class="review-nav prev" type="button" aria-label="Previous review"><i class="fas fa-chevron-left"></i></button>
            <div class="review-slides">
                <article class="featured-review is-active"><div><h3>Rene Portillo</h3><p class="stars">★★★★★ 5 Stars</p><p class="quote">I was very happy with service that was offered before during and after the installation. I will definitely recommend their services to others who need a roof.</p></div><img src="/images/reviews/rene-roof.jpg" alt="Rene Portillo roof"></article>
                <article class="featured-review"><div><h3>Lisa Marburger</h3><p class="stars">★★★★★ 5 Stars</p><p class="quote">I reached out to Montgomery with Core Four Roofing on Friday, July 14th, requesting a quote. I received the quote the same day. The quote was thorough, easy to read, and by far the best price. I asked how soon they could complete the job and Montgomery got back with me immediately stating they could do it the following Friday or Saturday. I requested they come Friday. Workers arrived and completed the job that Friday. The roof looks amazing and the job site was left with no evidence, short of the gorgeous new roof, that they had been there. Several of my neighbors have spoken with me stating how great the roof looks. Core Four Roofing and Montgomery get a 5 star A+++ from me. I would highly recommend them for any of your roofing needs.</p></div><img src="/images/reviews/lisa-roof.webp" alt="Lisa Marburger roof"></article>
                <article class="featured-review"><div><h3>Justin Chambers</h3><p class="stars">★★★★★ 5 Stars</p><p class="quote">They were quick to schedule and complete my roof using great materials at a good price. They don’t try to over charge you. I would definitely recommend them.</p></div><img src="/images/reviews/justin-roof.jpg" alt="Justin Chambers roof"></article>
                <article class="featured-review"><div><h3>Kevin Andrews</h3><p class="stars">★★★★★ 5 Stars</p><p class="quote">Cody was quick to meet the adjuster was helpful through the process. Not a flyby night company, very professional and knowledgeable.</p></div><img src="/images/reviews/kevin-roof.webp" alt="Kevin Andrews roof"></article>
            </div>
            <button class="review-nav next" type="button" aria-label="Next review"><i class="fas fa-chevron-right"></i></button>
            <div class="review-dots" aria-hidden="true"></div>
        </div>
    </div>
</section>
"""

INSTAGRAM = """
<section class="section section--ig instagram-section">
    <div class="wrap--wide"><div class="elfsight-app-71bb658f-56ca-4f80-8bfc-064bde22918c" data-elfsight-app-lazy></div></div>
</section>
"""

HOME = r"""
<section class="hero">
    <div class="hero-media"></div>
    <div class="wrap--wide hero-grid">
        <div class="hero-copy">
            <div class="hero-reviews"><div class="elfsight-app-267ccdad-1b32-4c62-9394-105914e96f0f" data-elfsight-app-lazy></div></div>
            <h1>Protect Your Business, Secure Your Home</h1>
            <p class="lead">Core Four Roofing provides premium commercial and residential roofing across Texas. Providing integrity, efficiency, quality, and affordability.</p>
            <div class="hero-actions"><a class="btn" href="tel:+12815410027">Call Us Today <i class="fas fa-arrow-right"></i></a></div>
        </div>
        <div class="hero-stage">
            <div class="float-card float-top"><h5>Warranty</h5><p>Lifetime Workmanship</p></div>
            """ + ba("/images/ba/core-four-TPO-roof-before.webp", "/images/ba/core-four-TPO-roof-after.webp", "Core Four TPO roof before", "Core Four TPO roof after") + """
            <div class="float-card float-bot"><h5>Accredited</h5><p>BBB A+ Rating</p></div>
        </div>
    </div>
</section>
<section class="section section--tight">
    <div class="wrap--full">
        <h2 class="logos-heading">Reliable Commercial &amp; Residential Roof Repair, Replacement &amp; Maintenance</h2>
        """ + LOGOS + """
    </div>
</section>
<section class="section">
    <div class="wrap">
        <div class="solutions-head">
            <h2 class="title-xl">Roofing Solutions that work for your bottom line</h2>
            <a class="btn" href="/preview/service-areas.html">Explore Our Services <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="solutions-grid" style="margin-top:40px">
            <div class="solutions-col">
                <div class="solution-card solution-card--green">
                    <span class="pill pill--white">Commercial</span>
                    <h3>Protecting Your Assets</h3>
                    <p>From TPO to EPDM and Metal, we provide durable, energy-efficient commercial roofing systems designed to last decades.</p>
                    <img src="/images/commercial-card.webp" alt="Commercial roofing project by Core Four Roofing">
                </div>
                """ + ba("/images/ba/Decra-12-scaled.jpg", "/images/ba/Decra-11-scaled.jpg", "Apartment roof before Decra replacement", "Apartment roof after Decra replacement") + """
            </div>
            <div class="solutions-col">
                <div class="solution-card solution-card--white">
                    <span class="pill pill--green">Residential</span>
                    <h3>We Know Texas Roofs</h3>
                    <p>Premium asphalt shingles and architectural metal roofs to boost curb appeal and weather protection.</p>
                    <div class="solution-media"><img src="/images/residential-card.webp" alt="Residential roofing project by Core Four Roofing"></div>
                </div>
                <div class="emergency-card">
                    <h4>24/7 Emergency</h4>
                    <p>Storm damage? We offer rapid response roof tarping and full insurance claim advocacy.</p>
                    <a class="btn" href="/preview/storm-emergency.html">Get Help Now <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="award-card"><h4>Recognized as one of the best in Houston, Texas</h4></div>
            </div>
        </div>
    </div>
</section>
""" + MAP + """
<section class="section section--steps">
    <div class="wrap">
        <h2 class="title-xl">A reliable new roof is just a few steps away</h2>
        <div class="steps-grid">
            <div>
                <div class="faq">
                    <details><summary><span class="step-num">1</span> Roof Consultation</summary><p>We provide a comprehensive roof inspection to evaluate damage, assess lifespan, and determine the most cost-effective solution for your commercial or residential property.</p></details>
                    <details><summary><span class="step-num">2</span> Professional Installation</summary><p>From TPO and EPDM for commercial buildings to premium asphalt shingles for homes, our licensed crews install your new roof efficiently—often in just 1-3 days.</p></details>
                    <details><summary><span class="step-num">3</span> Quality Control</summary><p>After installation, we perform a rigorous quality inspection. We leave your property spotless and ensure you have all warranty documentation for complete peace of mind.</p></details>
                </div>
                <a class="btn btn--grey" href="tel:+12815410027">Call for a Free Consultation <i class="fas fa-arrow-right"></i></a>
            </div>
            """ + ba("/images/ba/ad6b4429-cef8-4e0d-8be5-97760a5f48ca-compressed-scaled.webp", "/images/ba/27d812ee-6a31-44e6-a9c3-76435c4aa8b5-compressed-scaled.webp") + """
        </div>
    </div>
</section>
""" + REVIEWS + """
<section class="section section--principles">
    <div class="wrap principles-grid">
        <div class="guarantee-card">
            <span class="pill pill--green">The Guarantee</span>
            <h3>Protect Your Home With the Core Four Roofing Principles</h3>
            <p>For property owners who want the job done right the first time. We combine premium materials, expert installation, and transparent communication.</p>
            <div class="counter-card"><span class="number">1000+</span><p>Successful Jobs Completed</p></div>
            <a class="btn" href="tel:+12815410027">Call Us <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="stack">
            <div class="icon-box"><div class="icon-wrapper"><i class="fas fa-wallet" aria-hidden="true"></i></div><h3>Affordability</h3><p>We offer competitive pricing, transparent estimates, and stress-free financing options so you can protect your property without breaking the bank.</p></div>
            <div class="icon-box"><div class="icon-wrapper"><i class="fas fa-sync-alt" aria-hidden="true"></i></div><h3>Efficiency</h3><p>Fast turnaround times without sacrificing quality. Most residential replacements are completed in just 1-3 days, minimizing disruption to your life or business.</p></div>
            <div class="icon-box"><div class="icon-wrapper"><i class="fas fa-handshake" aria-hidden="true"></i></div><h3>Integrity</h3><p>Honest communication is our policy. We perform thorough inspections and never sell you what you don't need. We stand by our work and our word.</p></div>
            <div class="icon-box"><div class="icon-wrapper"><i class="fas fa-award" aria-hidden="true"></i></div><h3>Quality</h3><p>Master certified installers utilizing top-tier materials from industry-leading manufacturers. Built to withstand the toughest Texas weather.</p></div>
        </div>
    </div>
</section>
""" + INSTAGRAM


def wrap(title: str, body: str, home: bool = False, slug: str = "index") -> str:
    head = CHROME_HEAD.format(title=title, solid="" if home else " solid")
    return head.replace("<body>", f'<body class="page-{slug}">') + body + CHROME_FOOT


def main():
    OUT.mkdir(parents=True, exist_ok=True)
    (OUT / "index.html").write_text(wrap("Core Four Roofing | Commercial & Residential Roofing in Texas", HOME, home=True), encoding="utf-8")
    count = 1
    for path in sorted(PAGES.glob("*.json")):
        if path.stem == "home":
            continue
        data = json.loads(path.read_text())
        (OUT / f"{path.stem}.html").write_text(
            wrap(data.get("title") or path.stem, render_page(data), slug=path.stem), encoding="utf-8"
        )
        count += 1
    print(f"wrote {count} preview pages to {OUT}")


if __name__ == "__main__":
    main()
