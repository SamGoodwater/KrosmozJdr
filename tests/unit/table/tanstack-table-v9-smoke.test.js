import { describe, it, expect } from "vitest";
import { defineComponent, h } from "vue";
import { mount } from "@vue/test-utils";
import {
    columnVisibilityFeature,
    createPaginatedRowModel,
    createSortedRowModel,
    rowPaginationFeature,
    rowSortingFeature,
    tableFeatures,
    useTable,
} from "@tanstack/vue-table";

describe("TanStack Table 9", () => {
    it("trie et pagine côté client via useTable", () => {
        const Host = defineComponent({
            setup() {
                const features = tableFeatures({
                    columnVisibilityFeature,
                    rowSortingFeature,
                    rowPaginationFeature,
                    sortedRowModel: createSortedRowModel(),
                    paginatedRowModel: createPaginatedRowModel(),
                });
                const table = useTable({
                    features,
                    data: [
                        { id: 2, name: "b" },
                        { id: 1, name: "a" },
                    ],
                    columns: [
                        { id: "id", accessorKey: "id" },
                        { id: "name", accessorKey: "name" },
                    ],
                    state: {
                        sorting: [{ id: "id", desc: false }],
                        pagination: { pageIndex: 0, pageSize: 1 },
                    },
                });
                return {
                    pageIds: table.getRowModel().rows.map((r) => r.original.id),
                    allIds: table.getPrePaginatedRowModel().rows.map((r) => r.original.id),
                };
            },
            render() {
                return h("div");
            },
        });

        const wrapper = mount(Host);
        expect(wrapper.vm.pageIds).toEqual([1]);
        expect(wrapper.vm.allIds).toEqual([1, 2]);
        wrapper.unmount();
    });
});
