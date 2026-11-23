/**
 * Modèle Aptitude (bonus naturel de spécialisation).
 *
 * @example
 * const aptitude = new Aptitude(props.aptitude);
 * console.log(aptitude.reuseType);
 */
import { BaseModel } from '../BaseModel';
import { resolveEntityRouteHref } from '@/Composables/entity/entityRouteRegistry';

const REUSE_LABELS = {
    unlimited: 'Illimité',
    short_rest: 'Repos court',
    long_rest: 'Repos long',
    timed: 'Temporisé',
};

const DURATION_LABELS = {
    instant: 'Instantané',
    timed: 'Temporisé',
    permanent: 'Permanent',
    while_condition: 'Tant que…',
};

const RANGE_LABELS = {
    self: 'Soi',
    distance: 'Distance',
    sense: 'Sens',
};

const SENSE_LABELS = {
    hearing: 'Ouïe',
    sight: 'Vue',
    touch: 'Toucher',
    custom: 'Personnalisé',
};

export class Aptitude extends BaseModel {
    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get effect() {
        return this._data.effect || null;
    }

    get reuseType() {
        return this._data.reuse_type || 'unlimited';
    }

    get reuse_type() {
        return this.reuseType;
    }

    get reuseAmount() {
        const v = this._data.reuse_amount;
        return v === null || v === undefined || v === '' ? null : Number(v);
    }

    get reuseUnit() {
        return this._data.reuse_unit || null;
    }

    get durationType() {
        return this._data.duration_type || 'permanent';
    }

    get duration_type() {
        return this.durationType;
    }

    get durationAmount() {
        const v = this._data.duration_amount;
        return v === null || v === undefined || v === '' ? null : Number(v);
    }

    get durationUnit() {
        return this._data.duration_unit || null;
    }

    get durationNote() {
        return this._data.duration_note || null;
    }

    get rangeType() {
        return this._data.range_type || 'self';
    }

    get range_type() {
        return this.rangeType;
    }

    get rangeAmount() {
        const v = this._data.range_amount;
        return v === null || v === undefined || v === '' ? null : Number(v);
    }

    get rangeUnit() {
        return this._data.range_unit || null;
    }

    get rangeSense() {
        return this._data.range_sense || null;
    }

    get rangeNote() {
        return this._data.range_note || null;
    }

    get image() {
        return this._data.image || '';
    }

    get createdBy() {
        return this._data.createdBy || null;
    }

    get specializations() {
        return this._data.specializations || [];
    }

    get reuseLabel() {
        if (this.reuseType === 'timed' && this.reuseAmount && this.reuseUnit) {
            return `${this.reuseAmount} ${this.reuseUnit}`;
        }
        return REUSE_LABELS[this.reuseType] || this.reuseType;
    }

    get durationLabel() {
        if (this.durationType === 'timed' && this.durationAmount && this.durationUnit) {
            return `${this.durationAmount} ${this.durationUnit}`;
        }
        if (this.durationType === 'while_condition' && this.durationNote) {
            return this.durationNote;
        }
        return DURATION_LABELS[this.durationType] || this.durationType;
    }

    get rangeLabel() {
        if (this.rangeType === 'distance' && this.rangeAmount) {
            return `${this.rangeAmount} ${this.rangeUnit || 'm'}`;
        }
        if (this.rangeType === 'sense') {
            const sense = SENSE_LABELS[this.rangeSense] || this.rangeSense || 'Sens';
            return this.rangeNote ? `${sense} (${this.rangeNote})` : sense;
        }
        return RANGE_LABELS[this.rangeType] || this.rangeType;
    }

    toCell(fieldKey, options = {}) {
        const baseCell = super.toCell(fieldKey, options);
        if (baseCell && (baseCell.type !== 'text' || (baseCell.value && baseCell.value !== '-'))) {
            return baseCell;
        }

        const { size = 'md', format = {} } = options;

        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
            case 'effect':
                return this._toTextCell(fieldKey, format, size);
            case 'reuse_type':
                return {
                    type: 'badge',
                    value: this.reuseLabel,
                    params: {
                        filterValue: this.reuseType,
                        sortValue: this.reuseType,
                        searchValue: this.reuseLabel,
                    },
                };
            case 'duration_type':
                return {
                    type: 'badge',
                    value: this.durationLabel,
                    params: {
                        filterValue: this.durationType,
                        sortValue: this.durationType,
                        searchValue: this.durationLabel,
                    },
                };
            case 'range_type':
                return {
                    type: 'badge',
                    value: this.rangeLabel,
                    params: {
                        filterValue: this.rangeType,
                        sortValue: this.rangeType,
                        searchValue: this.rangeLabel,
                    },
                };
            default:
                return baseCell;
        }
    }

    _toNameCell(format, size, options) {
        const name = this.name || '-';
        const href = options.href || resolveEntityRouteHref('aptitudes', 'show', this.id) || `/entities/aptitudes/${this.id}`;

        return {
            type: 'route',
            value: name,
            params: {
                href,
                tooltip: name === '-' ? '' : name,
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 20 : null),
                searchValue: name === '-' ? '' : name,
                sortValue: name,
            },
        };
    }

    _toTextCell(fieldKey, format, size) {
        const value = this[fieldKey] || '-';
        return {
            type: 'text',
            value,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 30 : (size === 'md' ? 50 : null)),
                searchValue: value === '-' ? '' : String(value),
                sortValue: value,
            },
        };
    }

    toFormData() {
        return {
            name: this.name,
            description: this.description,
            effect: this.effect,
            reuse_type: this.reuseType,
            reuse_amount: this.reuseAmount,
            reuse_unit: this.reuseUnit,
            duration_type: this.durationType,
            duration_amount: this.durationAmount,
            duration_unit: this.durationUnit,
            duration_note: this.durationNote,
            range_type: this.rangeType,
            range_amount: this.rangeAmount,
            range_unit: this.rangeUnit,
            range_sense: this.rangeSense,
            range_note: this.rangeNote,
            state: this.state,
            read_level: this.readLevel,
            write_level: this.writeLevel,
            image: this.image,
        };
    }
}

export default Aptitude;
