import { BaseModel } from "../BaseModel";

/**
 * Langue (référentiel M2M classes / monstres / …).
 */
export class Language extends BaseModel {
    get id() {
        return this._data.id ?? null;
    }

    get name() {
        return this._data.name ?? "";
    }

    get description() {
        return this._data.description ?? null;
    }

    get color() {
        return this._data.color ?? "#64748b";
    }

    get sortOrder() {
        return Number(this._data.sort_order ?? this._data.sortOrder ?? 0);
    }
}
