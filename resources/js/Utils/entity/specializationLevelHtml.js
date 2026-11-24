/**
 * Lecture d’un palier de spécialisation : HTML CMS (maîtrises, aptitudes, styles…)
 * + extraction des capacités choix / variantes pour un rendu Vue densifié.
 */

import { resolveSpecializationStyle } from "@/Utils/entity/specializationStyles";

/**
 * @param {string} html
 * @returns {{ aptitudePick: number, capacityPick: number }}
 */
export function extractSpecializationLevelPicks(html) {
    const raw = String(html || "");
    const aptitude = /<h2[^>]*>\s*Aptitudes\s*[—–-]\s*choisis-en\s+(\d+)/i.exec(raw);
    const capacity = /<h2[^>]*>\s*Capacités\s*[—–-]\s*choisis-en\s+(\d+)/i.exec(raw);

    return {
        aptitudePick: aptitude ? Number(aptitude[1]) || 0 : 0,
        capacityPick: capacity ? Number(capacity[1]) || 0 : 0,
    };
}

/**
 * @param {Element} root
 * @returns {string[]}
 */
function collectCapabilityNames(root) {
    /** @type {string[]} */
    const names = [];
    const text = String(root?.innerHTML || "");
    const shortcode = /\[\[kref:entity:capabilities:[^:\]]+:([^\]]+)\]\]/gi;
    let m;
    while ((m = shortcode.exec(text)) !== null) {
        const name = String(m[1] || "").trim();
        if (name) names.push(name);
    }
    // Krefs résolus (span.kref) : libellé visible = nom de capacité.
    root?.querySelectorAll?.("span.kref").forEach((el) => {
        const label = String(
            el.getAttribute("data-kref-label") || el.textContent || "",
        ).trim();
        if (label) names.push(label);
    });
    return names;
}

/**
 * Nom de capacité d’un <li> variante (strong / kref en tête, pas tout le texte d’effet).
 *
 * @param {Element} li
 * @returns {string}
 */
function capabilityNameFromLi(li) {
    const fromKref = collectCapabilityNames(li);
    if (fromKref.length) return fromKref[0];
    const strong = li.querySelector(":scope > strong");
    if (strong) return String(strong.textContent || "").trim();
    return "";
}

/**
 * @param {Element} li
 * @returns {{ parentNames: string[], rows: { styleKey: string, capabilityName: string, effectHtml: string }[] }}
 */
function parseStyleVariantLi(li) {
    const styleKey = String(li.getAttribute("data-spe-style") || "").trim();
    const names = collectCapabilityNames(li);
    const nestedLis = li.querySelectorAll(":scope > ul > li");

    if (nestedLis.length > 0) {
        /** @type {{ styleKey: string, capabilityName: string, effectHtml: string }[]} */
        const rows = [];
        for (const child of nestedLis) {
            const childKey =
                String(child.getAttribute("data-spe-style") || "").trim() || styleKey;
            const capabilityName = capabilityNameFromLi(child);
            const clone = child.cloneNode(true);
            const strong = clone.querySelector("strong");
            if (strong) strong.remove();
            const effectHtml = String(clone.innerHTML || "")
                .replace(/^\s*[—–-]\s*/, "")
                .trim();
            rows.push({
                styleKey: childKey,
                capabilityName,
                effectHtml,
            });
        }
        return { parentNames: names, rows };
    }

    return {
        parentNames: names,
        rows: [
            {
                styleKey,
                capabilityName: capabilityNameFromLi(li) || names[0] || "",
                effectHtml: "",
            },
        ],
    };
}

/**
 * Remplace les titres de style (h3 Musicien…) par des pastilles `.spe-style`.
 *
 * @param {Element} root
 * @param {string} [specializationSlug='artiste']
 */
export function decorateSpecializationStyleHeadings(root, specializationSlug = "artiste") {
    if (!root?.querySelectorAll) return;
    root.querySelectorAll("h3, h4").forEach((heading) => {
        if (heading.querySelector(".spe-style")) return;
        const label = String(heading.textContent || "").trim();
        if (!label || label.length > 40) return;
        const token = resolveSpecializationStyle(label, specializationSlug);
        if (!token) return;
        const span = root.ownerDocument.createElement("span");
        span.className = "spe-style";
        span.setAttribute("data-spe-style", token.key);
        span.textContent = token.name;
        heading.replaceChildren(span);
    });
}

/**
 * Même décoration sur une chaîne HTML (présentation CMS hors LevelBody).
 *
 * @param {string} html
 * @param {string} [specializationSlug='artiste']
 * @returns {string}
 */
export function decorateSpecializationStyleHtml(html, specializationSlug = "artiste") {
    const raw = String(html || "");
    if (!raw.trim() || typeof DOMParser === "undefined") return raw;
    const doc = new DOMParser().parseFromString(`<div id="spe-deco-root">${raw}</div>`, "text/html");
    const root = doc.getElementById("spe-deco-root");
    if (!root) return raw;
    decorateSpecializationStyleHeadings(root, specializationSlug);
    return root.innerHTML;
}

/**
 * Prépare le HTML narrative + métadonnées pour un palier.
 *
 * @param {string} html
 * @param {{ specializationSlug?: string }} [options]
 * @returns {{
 *   narrativeHtml: string,
 *   aptitudePick: number,
 *   capacityPick: number,
 *   excludeCapabilityNames: string[],
 *   styleVariants: { styleKey: string, capabilityName: string, effectHtml: string }[],
 * }}
 */
export function prepareSpecializationLevelHtml(html, options = {}) {
    const specializationSlug = options.specializationSlug || "artiste";
    const raw = String(html || "");
    const picks = extractSpecializationLevelPicks(raw);
    /** @type {string[]} */
    const excludeCapabilityNames = [];
    /** @type {{ styleKey: string, capabilityName: string, effectHtml: string }[]} */
    const styleVariants = [];

    if (!raw.trim()) {
        return {
            narrativeHtml: "",
            aptitudePick: picks.aptitudePick,
            capacityPick: picks.capacityPick,
            excludeCapabilityNames: [],
            styleVariants: [],
        };
    }

    if (typeof DOMParser === "undefined") {
        const narrativeHtml = raw
            .replace(
                /<h2[^>]*>\s*Capacités[\s\S]*?<\/h2>\s*(?:<ul[\s\S]*?<\/ul>|<p[\s\S]*?<\/p>)?/gi,
                "",
            )
            .trim();
        return {
            narrativeHtml,
            aptitudePick: picks.aptitudePick,
            capacityPick: picks.capacityPick,
            excludeCapabilityNames: [],
            styleVariants: [],
        };
    }

    const doc = new DOMParser().parseFromString(`<div id="spe-level-root">${raw}</div>`, "text/html");
    const root = doc.getElementById("spe-level-root");
    if (!root) {
        return {
            narrativeHtml: raw.trim(),
            aptitudePick: picks.aptitudePick,
            capacityPick: picks.capacityPick,
            excludeCapabilityNames: [],
            styleVariants: [],
        };
    }

    const headings = Array.from(root.querySelectorAll("h2"));
    for (const h2 of headings) {
        const title = String(h2.textContent || "").trim();
        if (!/^Capacités\b/i.test(title) && !/^Selon ton style\b/i.test(title)) continue;

        const list = h2.nextElementSibling;
        if (list && list.tagName === "UL") {
            for (const child of Array.from(list.children)) {
                if (child.tagName !== "LI") continue;
                if (child.querySelector("ul") || child.getAttribute("data-spe-style")) {
                    const parsed = parseStyleVariantLi(child);
                    excludeCapabilityNames.push(...parsed.parentNames);
                    const fallbackKeys = ["musicien", "saltimbanque", "troubadour", "voyant"];
                    let fallbackIdx = 0;
                    for (const row of parsed.rows) {
                        if (row.capabilityName) {
                            excludeCapabilityNames.push(row.capabilityName);
                        }
                        if (!row.styleKey && fallbackIdx < fallbackKeys.length) {
                            row.styleKey = fallbackKeys[fallbackIdx];
                            fallbackIdx += 1;
                        }
                        styleVariants.push(row);
                    }
                }
            }
        }

        h2.remove();
        if (list && (list.tagName === "UL" || list.tagName === "P")) {
            list.remove();
        }
    }

    root.querySelectorAll("h2").forEach((el) => {
        el.classList.add("spe-level-h2");
    });

    // Listes de maîtrises : marqueur pour le CSS (libellé fort + aération).
    root.querySelectorAll("h2.spe-level-h2").forEach((h2) => {
        if (!/^Maîtrises\b/i.test(String(h2.textContent || "").trim())) return;
        const list = h2.nextElementSibling;
        if (list?.tagName === "UL") list.classList.add("spe-masteries-list");
    });

    decorateSpecializationStyleHeadings(root, specializationSlug);
    tidySpecializationNarrative(root);

    return {
        narrativeHtml: root.innerHTML.trim(),
        aptitudePick: picks.aptitudePick,
        capacityPick: picks.capacityPick,
        excludeCapabilityNames: Array.from(
            new Set(excludeCapabilityNames.map((n) => n.toLowerCase())),
        ),
        styleVariants,
    };
}

/**
 * Allège le HTML déjà seedé : pas de récap « À ce niveau », pas de « : » sous le titre
 * d’une maîtrise, pas de ligne vide (Aucune), pas de « Rituel : oui/non » sur un style.
 * La 2ᵉ sauvegarde devient une phrase.
 *
 * @param {HTMLElement} root
 */
function tidySpecializationNarrative(root) {
    root.querySelectorAll("p").forEach((p) => {
        const strong = p.querySelector(":scope > strong");
        const label = String(strong?.textContent || "").trim();
        const whole = String(p.textContent || "").trim();
        if (/^À ce (niveau|palier)\b/i.test(whole) || /^À ce (niveau|palier)$/i.test(label)) {
            p.remove();
            return;
        }
        if (/^Rituel$/i.test(label) && /^Rituel\s*:\s*(oui|non)\b/i.test(whole)) {
            p.remove();
            return;
        }
        if (!/^2(?:e|ᵉ) sauvegarde$/i.test(label)) {
            return;
        }
        const rest = document.createElement("span");
        let node = strong.nextSibling;
        while (node) {
            const next = node.nextSibling;
            rest.appendChild(node);
            node = next;
        }
        const first = rest.firstChild;
        if (first && first.nodeType === Node.TEXT_NODE) {
            first.textContent = String(first.textContent || "").replace(/^\s*:\s*/, "");
        }
        const sentence = document.createElement("p");
        sentence.append("Tu maîtrises les jets de sauvegarde liés à ");
        while (rest.firstChild) sentence.appendChild(rest.firstChild);
        const last = sentence.lastChild;
        if (last && last.nodeType === Node.TEXT_NODE) {
            last.textContent = String(last.textContent || "").replace(/\s*\.\s*$/, "").trimEnd();
        }
        sentence.append(".");
        p.replaceWith(sentence);
    });

    root.querySelectorAll("ul.spe-masteries-list > li").forEach((li) => {
        const strong = li.querySelector(":scope > strong");
        const first = strong ? strong.nextSibling : li.firstChild;
        if (first && first.nodeType === Node.TEXT_NODE) {
            first.textContent = String(first.textContent || "").replace(/^\s*:\s*/, "");
            if (String(first.textContent || "").trim() === "") first.remove();
        }
        const body = String(li.textContent || "")
            .replace(String(strong?.textContent || ""), "")
            .trim();
        if (/^(aucune?s?|aucun(?: pour l['’]instant)?)\.?$/i.test(body)) {
            li.remove();
        }
    });

    root.querySelectorAll("ul.spe-masteries-list").forEach((list) => {
        if (list.querySelector("li")) return;
        const heading = list.previousElementSibling;
        list.remove();
        if (heading?.tagName === "H2" && /^Maîtrises\b/i.test(String(heading.textContent || ""))) {
            heading.remove();
        }
    });
}

/**
 * @deprecated Utiliser {@link prepareSpecializationLevelHtml}.
 * @param {string} html
 * @returns {string}
 */
export function stripSpecializationCapabilityBlocks(html) {
    return prepareSpecializationLevelHtml(html).narrativeHtml;
}

/**
 * @param {object} capability
 * @returns {number}
 */
export function specializationCapabilityPivotLevel(capability) {
    const raw = capability?._data ?? capability;
    const pivot = raw?.pivot;
    const fromPivot = Number(pivot?.level);
    if (Number.isFinite(fromPivot) && fromPivot > 0) return fromPivot;
    return 0;
}

/**
 * @param {object|null|undefined} section
 * @returns {string}
 */
export function sectionContentHtml(section) {
    if (!section) return "";
    const data = section.data ?? section._data?.data;
    return String(data?.content ?? section.content ?? "").trim();
}
