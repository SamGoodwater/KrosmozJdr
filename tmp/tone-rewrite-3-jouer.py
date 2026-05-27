#!/usr/bin/env python3
"""Rewrite tone for 3-Jouer rule markdown files."""
import re
from pathlib import Path

ROOT = Path("/var/www/KrosmozJdr/docs/400- Jeu/420- Règles/3-Jouer")

DESC_MAP = {
    "3.1.1": "**Description** : Comment tu te déplaces et interagis avec le monde hors combat — terrains, visibilité, lignes de vue, PNJ et événements. En MJ, tu poses le cadre ; à table, tu explores.",
    "3.1.2": "**Description** : Comment le temps file pendant l'aventure hors combat — tours d'exploration, repos, jour/nuit et événements liés au calendrier.",
    "3.1.3": "**Description** : Détecter, déclencher et désamorcer les pièges (mécaniques, magiques, environnement). En MJ, tu les poses ; à table, tu testes ta vigilance.",
    "3.1.4": "**Description** : Réactions rapides hors combat — surprise, embuscade, joutes sociales, tests opposés et événements qui demandent une décision immédiate.",
    "3.2.1": "**Description** : Comment un combat démarre — déclencheur, placement, surprise et initiative. Tu sais dans quel ordre tu joueras.",
    "3.2.2": "**Description** : Un tour de combat en détail — [[kref:characteristic:action_points_creature|PA]], [[kref:characteristic:movement_points_creature|PM]], [[kref:characteristic:range_creature|PO]], actions et résolution des attaques/sorts, avec exemples à table.",
    "3.2.3": "**Description** : Tacle, fuite et réactions spéciales — l'équivalent Krosmoz des opportunités de D&D, sans attaque d'opportunité classique.",
    "3.2.4": "**Description** : Points de vie, boucliers, soins, inconscience et mort — tout ce qu'il faut pour suivre ta santé (et celle des alliés) en jeu.",
    "3.2.5": "**Description** : Traits permanents et états temporaires — ce qui te colle à la peau ou ne dure qu'un combat, et comment ça interagit avec sorts et aptitudes.",
    "3.3.1": "**Description** : Les familles de sorts (classe, apprenables, consommables, créature) — qui peut les avoir et comment les utiliser.",
    "3.3.2": "**Description** : Coûts, portée, cibles, lignes de vue et résolution (touche, sauvegarde, zone) — le cœur mécanique du lancement.",
    "3.3.3": "**Description** : Apprendre ou changer un sort — parchemins, maître, temple, temps d'étude et limites de sorts connus.",
    "3.3.4": "**Description** : Ta [[kref:characteristic:wakfu_reserve_creature|réserve de Wakfu]] — calcul, dépense hors combat, récupération et risques si tu la vides.",
    "3.3.5": "**Description** : Variantes de sorts et personnalisation — choix au déblocage, changement au temple, accord du MJ pour les ajustements.",
    "3.4.1": "**Description** : Les aptitudes viennent surtout de ta spécialisation — roleplay, exploration et parfois combat.",
    "3.4.2": "**Description** : Les capacités — plus orientées action et combat que les aptitudes, avec leurs coûts et limites.",
    "3.4.3": "**Description** : Utiliser aptitudes et capacités à table — hors combat (Wakfu) et en combat ([[kref:characteristic:action_points_creature|PA]]).",
    "3.4.4": "**Description** : Quand aptitudes, capacités et sorts se cumulent ou se répondent — synergies et pièges à éviter.",
    "3.4.5": "**Description** : Variantes et house rules — adapter le jeu chez toi sans casser l'équilibre.",
    "3.5.1": "**Description** : La liste des compétences et à quoi elles servent — physique, mental, social, technique.",
    "3.5.2": "**Description** : Faire un test de compétence — quand le MJ en demande un, comment le résoudre.",
    "3.5.3": "**Description** : Maîtrise et expertise — bonus, progression et impact sur tes jets.",
    "3.5.4": "**Description** : Modificateurs et degrés de difficulté — avantage, désavantage, DD et circonstances.",
    "3.5.5": "**Description** : Utiliser les compétences pour enrichir la narration — pas seulement pour « passer un jet ».",
}

REPLACEMENTS = [
    ("Le MJ peut", "Tu peux, en MJ,"),
    ("Le MJ détermine", "En MJ, tu détermines"),
    ("Le MJ doit", "En MJ, tu dois"),
    ("Le MJ utilise", "En MJ, tu utilises"),
    ("Le MJ lance", "En MJ, tu lances"),
    ("Le MJ suit", "En MJ, tu suis"),
    ("Le MJ fixe", "En MJ, tu fixes"),
    ("Le MJ choisit", "En MJ, tu choisis"),
    ("Le MJ classe", "En MJ, tu classes"),
    ("Le ou la MJ", "Tu, en MJ,"),
    ("Tu détermines (en MJ)", "En MJ, tu détermines"),
    ("Les joueurs doivent", "Tu dois"),
    ("Les joueurs peuvent", "Tu peux"),
    ("Les joueurs/joueuses", "Toi"),
    ("les joueurs/joueuses", "toi"),
    ("Le joueur/joueuse", "Toi"),
    ("le joueur/joueuse", "toi"),
    ("Les joueurs ne peuvent pas", "Tu ne peux pas"),
    ("Les joueurs ne peuvent", "Tu ne peux"),
    ("Les joueurs", "Toi et ton groupe"),
    ("les joueurs", "toi et ton groupe"),
    ("Le joueur", "Toi"),
    ("le joueur", "toi"),
    ("On considère qu'il est", "Tu es considéré comme"),
    ("On considère qu'", "On te considère comme "),
    ("Identifiez d'abord", "Identifie d'abord"),
    ("Identifiez", "Identifie"),
    ("surveillez la", "surveille ta"),
    ("Surveillez", "Surveille"),
    ("consignez", "note"),
    ("Consignez", "Note"),
    ("Tenez un registre", "Tiens un registre"),
    ("tenez un registre", "tiens un registre"),
    ("Récompensez", "Récompense"),
    ("Soyez flexible", "Reste flexible"),
    ("Permets la créativité", "Laisse place à la créativité"),
    ("Ajustez la difficulté", "Ajuste la difficulté"),
    ("Construisez des relations", "Construis des relations"),
    ("N'hésitez pas", "N'hésite pas"),
    ("Recherchez des maîtres", "Cherche des maîtres"),
    ("Négociez", "Négocie"),
    ("les MJ sont invités", "tu es invité, en MJ,"),
    ("Les MJ sont invités", "Tu es invité, en MJ,"),
    ("Conseils pour les joueurs", "Conseils pour toi"),
    ("### Conseils pour le MJ", "### Conseils pour toi, en MJ"),
    ("Conseils pour le MJ", "Conseils pour toi, en MJ"),
    ("Les joueurs doivent équilibrer", "Tu dois équilibrer"),
    ("Gestion par le MJ", "En MJ : ce que tu gères"),
    ("Création de pièges (MJ)", "Création de pièges (en MJ)"),
    ("Utilisation par le MJ", "En MJ : utiliser ces sorts"),
    ("> **Conseil MJ**", "> **Conseil pour toi, en MJ**"),
    ("extended downtime", "repos prolongé"),
    ("Sophie joue", "Sophie incarne"),
    ("La plupart des MJ font", "En MJ, tu peux faire"),
    ("Le MJ a le dernier mot", "En MJ, tu as le dernier mot"),
    ("Le MJ indique", "En MJ, tu indiques"),
    ("C'est au MJ de décider", "C'est à toi, en MJ, de décider"),
    ("Les personnages interagissent", "Tu interagis"),
    ("Les personnages peuvent", "Tu peux"),
    ("Les personnages qui", "Si tu"),
    ("Les personnages sont", "Tu es"),
    ("Les personnages ne", "Tu ne"),
    ("Les personnages agissent", "Tu agis"),
    ("Les personnages doivent", "Tu dois"),
    ("Les personnages", "Ton groupe"),
    ("Il devra passer", "Tu devras passer"),
    ("il devra passer", "tu devras passer"),
    ("Il devra", "Tu devras"),
    ("Variantess", "Variantes"),
    ("Récompense toi et ton groupe qui prennent", "Récompense-toi si tu prends"),
    ("Laisse les toi et ton groupe", "Laisse à ton groupe"),
    ("ne surprends pas toujours les toi et ton groupe", "ne surprends pas toujours ton groupe"),
    ("Assure-toi que les toi et ton groupe", "Assure-toi que ton groupe"),
    ("garder les toi et ton groupe attentifs", "garder ton groupe attentif"),
]

FIX_MJ = [
    ("Tu, en MJ, détermine qui", "En MJ, tu détermines qui"),
    ("Tu, en MJ, compare les", "En MJ, tu compares les"),
    ("Tu, en MJ, lance un", "En MJ, tu lances un"),
    ("Tu, en MJ, classe les", "En MJ, tu classes les"),
    ("Tu, en MJ, peut décider", "En MJ, tu peux décider"),
    ("Tu, en MJ, peut les", "En MJ, tu peux les"),
    ("Tu, en MJ, choisit quelle", "En MJ, tu choisis quelle"),
]


def file_key(stem: str) -> str:
    parts = stem.split("-")
    if len(parts) >= 2 and parts[0].startswith("3."):
        return f"{parts[0]}.{parts[1]}"
    return parts[0]


def transform(text: str, key: str) -> str:
    if key in DESC_MAP:
        text = re.sub(r"\*\*Description\*\* :[^\n]+", DESC_MAP[key], text, count=1)
    for old, new in REPLACEMENTS:
        text = text.replace(old, new)
    for old, new in FIX_MJ:
        text = text.replace(old, new)
    return text


def main():
    stats = []
    for fp in sorted(ROOT.rglob("*.md")):
        orig = fp.read_text(encoding="utf-8")
        new = transform(orig, file_key(fp.stem))
        if new != orig:
            fp.write_text(new, encoding="utf-8")
            stats.append((fp.relative_to(ROOT), sum(1 for a, b in zip(orig, new) if a != b)))
    print(f"Updated {len(stats)}/24 files")
    for rel, chars in sorted(stats, key=lambda x: -x[1])[:12]:
        print(f"  {chars:6d} chars | {rel}")


if __name__ == "__main__":
    main()
