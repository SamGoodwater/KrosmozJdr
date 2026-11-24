/**
 * Modèle Aptitude (bonus naturel de spécialisation).
 *
 * @example
 * const aptitude = new Aptitude(props.aptitude);
 * console.log(aptitude.reuse);
 */
import { BaseModel } from '../BaseModel';
import { resolveEntityRouteHref } from '@/Composables/entity/entityRouteRegistry';

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

    get reuse() {
        return this._data.reuse || '';
    }

    get duration() {
        return this._data.duration || '';
    }

    get range() {
        return this._data.range || '';
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
            case 'reuse':
            case 'duration':
            case 'range':
                return this._toTextCell(fieldKey, format, size);
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
            reuse: this.reuse,
            duration: this.duration,
            range: this.range,
            state: this.state,
            read_level: this.readLevel,
            write_level: this.writeLevel,
            image: this.image,
        };
    }
}

export default Aptitude;
