/**
 * Infobulle du champ prix (kamas).
 *
 * @param {{ entityType: string, priceCalculated?: number|null, priceCustom?: number|null }} options
 * @returns {string}
 *
 * @example
 * buildKamasPriceTooltip({ entityType: 'item', priceCalculated: 1500, priceCustom: 0 })
 */
export function buildKamasPriceTooltip({ entityType, priceCalculated = null, priceCustom = null } = {}) {
  if (entityType === 'resource') {
    return 'Prix proposé par Dofus.';
  }

  const auto =
    priceCalculated === null || priceCalculated === undefined || priceCalculated === ''
      ? '—'
      : String(Math.round(Number(priceCalculated)));
  const formula =
    entityType === 'consumable'
      ? 'Somme des prix des ressources de la recette.'
      : 'Bonus de caractéristiques + 150 kamas × niveau + 200 kamas × rareté.';
  const parts = [`Prix automatique : ${auto} kamas.`, formula];
  const custom = Number(priceCustom);
  if (Number.isFinite(custom) && custom !== 0) {
    parts.push('Le total affiché a été ajusté manuellement.');
  }

  return parts.join(' ');
}
