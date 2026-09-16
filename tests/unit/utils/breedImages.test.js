import { describe, expect, it } from "vitest";
import {
    breedFullUrl,
    breedHasBothFullImages,
    breedLogoFemaleUrl,
    breedLogoMaleUrl,
} from "@/Utils/entity/breedImages.js";

const iop = {
    logo_male: "/storage/images/breeds/iop/logo_m.png",
    logo_female: "/storage/images/breeds/iop/logo_f.png",
    image_full_male: "/storage/images/breeds/iop/full_m.png",
    image_full_female: "/storage/images/breeds/iop/full_f.png",
    image: "/storage/images/breeds/iop/full_m.png",
    icon: "/storage/images/breeds/iop/symbol-bw.png",
};

describe("breedImages", () => {
    it("prend le logo mâle en condensé et femelle en déployé", () => {
        expect(breedLogoMaleUrl(iop)).toBe("/storage/images/breeds/iop/logo_m.png");
        expect(breedLogoFemaleUrl(iop)).toBe("/storage/images/breeds/iop/logo_f.png");
        expect(breedLogoMaleUrl({ _data: iop })).toBe("/storage/images/breeds/iop/logo_m.png");
    });

    it("bascule le full mâle / femelle", () => {
        expect(breedFullUrl(iop, "m")).toBe("/storage/images/breeds/iop/full_m.png");
        expect(breedFullUrl(iop, "f")).toBe("/storage/images/breeds/iop/full_f.png");
        expect(breedHasBothFullImages(iop)).toBe(true);
        expect(breedHasBothFullImages({ image_full_male: iop.image_full_male })).toBe(false);
    });
});
