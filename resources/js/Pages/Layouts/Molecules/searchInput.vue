<script setup>
import tooltips from "@/Pages/Atoms/feedback/Tooltip.vue";
import { ref, onMounted, onUnmounted } from "vue";
const appName = ref(import.meta.env.VITE_APP_NAME);

// Générer un ID unique
const searchBarId = `searchBar-${Math.random().toString(36).substr(2, 9)}`;

const handleKeydown = (event) => {
    if (event.altKey && event.key === "k") {
        document.getElementById(searchBarId).focus();
    }
};

onMounted(() => {
    window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeydown);
});
</script>

<template>
    <div>
        <tooltips bgColor="bg-secondary-900/90">

                <label class="input input-bordered flex items-center gap-2">
                    <input
                        :id="searchBarId"
                        type="text"
                        class="grow lg:w-64 md:w-32 sm:w-16 max-sm:w-auto"
                        placeholder="Rechercher"
                    />
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 16 16"
                        fill="currentColor"
                        class="h-4 w-4 opacity-70"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </label>


            <template #content>
                <div class="flex flex-nowrap justify-content-between gap-2">
                    <p class="text-sm">Rechercher sur {{ appName }} :</p>
                    <p>
                        <span class="flex flex-nowrap align-items-center"
                            ><kbd class="kbd kbd-sm">alt</kbd> +
                            <kbd class="kbd kbd-sm">k</kbd></span
                        >
                    </p>
                </div>

                <div class="flex justify-around gap-1">
                    <ul>
                        <li>
                            <span class="badge bg-page-800 m-0.5">pages</span>
                        </li>
                        <li>
                            <span class="badge bg-classe-800 m-0.5"
                                >classes</span
                            >
                        </li>
                        <li>
                            <span class="badge bg-item-800 m-0.5">items</span>
                        </li>
                        <li>
                            <span class="badge bg-resource-800 m-0.5"
                                >resources</span
                            >
                        </li>
                        <li>
                            <span class="badge bg-consumable-800 m-0.5"
                                >consommables</span
                            >
                        </li>
                        <li>
                            <span class="badge bg-campaign-800 m-0.5"
                                >campagnes</span
                            >
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <span class="badge bg-mob-800 m-0.5"
                                >créatures</span
                            >
                        </li>
                        <li>
                            <span class="badge bg-spell-800 m-0.5">sorts</span>
                        </li>
                        <li><span class="badge bg-npc-800 m-0.5">PNJ</span></li>
                        <li>
                            <span class="badge bg-specialization-800 m-0.5"
                                >Spécialisations</span
                            >
                        </li>
                        <li>
                            <span class="badge bg-scenario-800 m-0.5"
                                >scénarios</span
                            >
                        </li>
                        <li class="m-0.5">etc.</li>
                    </ul>
                </div>
            </template>
        </tooltips>
    </div>
</template>
