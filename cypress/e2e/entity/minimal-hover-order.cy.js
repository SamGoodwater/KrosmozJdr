const TECHNICAL_FIELDS = [
    "id",
    "slug",
    "state",
    "is_public",
    "read_level",
    "write_level",
    "created_at",
    "updated_at",
    "deleted_at",
];

const CASES = [
    { entityType: "item", path: "/entities/items" },
    { entityType: "spell", path: "/entities/spells" },
    { entityType: "monster", path: "/entities/monsters" },
    { entityType: "resource", path: "/entities/resources" },
];

function setStoredViewMode(win, entityType) {
    win.localStorage.setItem(`entity_view_format_${entityType}`, "minimal");
    win.localStorage.setItem(`entity_view_minimal_display_mode_${entityType}`, "hover");
}

function openFirstRowModal() {
    cy.get("table tbody tr", { timeout: 20000 }).should("have.length.greaterThan", 0);
    cy.get("table tbody tr").first().dblclick({ force: true });
    cy.get(".modal.modal-open", { timeout: 10000 }).should("exist");
}

function ensureMinimalHoverMode() {
    cy.contains("button", "Options d’affichage", { timeout: 10000 }).click({ force: true });
    cy.contains("button", "Minimal", { timeout: 10000 }).click({ force: true });
    cy.contains("button", "Compact → étendu au survol", { timeout: 10000 }).click({ force: true });
}

function assertTechnicalFieldsRenderedAtEnd() {
    cy.get("[data-cy='entity-minimal-card']", { timeout: 10000 })
        .first()
        .trigger("mouseenter", { force: true });

    cy.get("[data-cy='entity-minimal-expanded']", { timeout: 10000 }).should("be.visible");
    cy.get("[data-cy='entity-minimal-expanded'] [data-field-key]").then(($rows) => {
        const keys = [...$rows].map((el) => el.getAttribute("data-field-key"));
        expect(keys.length, "au moins un champ étendu").to.be.greaterThan(0);

        let reachedTechnicalPart = false;
        for (const key of keys) {
            const isTechnical = TECHNICAL_FIELDS.includes(String(key));
            if (isTechnical) {
                reachedTechnicalPart = true;
                continue;
            }
            expect(
                reachedTechnicalPart,
                `Le champ métier "${key}" ne doit pas apparaître après les champs techniques`,
            ).to.equal(false);
        }
    });
}

describe("Minimal hover field ordering", () => {
    CASES.forEach(({ entityType, path }) => {
        it(`keeps technical fields at the end for ${entityType}`, () => {
            cy.visit(path, {
                onBeforeLoad(win) {
                    setStoredViewMode(win, entityType);
                },
            });

            openFirstRowModal();
            ensureMinimalHoverMode();
            assertTechnicalFieldsRenderedAtEnd();
        });
    });
});
