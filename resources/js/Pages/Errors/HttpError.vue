<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    status: {
        type: Number,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    hint: {
        type: String,
        default: null,
    },
});

const canGoBack = computed(() => typeof window !== "undefined" && window.history.length > 1);

function goBack() {
    if (!canGoBack.value) {
        return;
    }
    window.history.back();
}
</script>

<template>
    <Head :title="`${status} - ${title}`" />

    <div class="min-h-screen w-full bg-base-200 text-base-content">
        <div class="mx-auto flex min-h-screen w-full max-w-4xl items-center justify-center p-6">
            <div class="card w-full border border-base-300 bg-base-100 shadow-2xl">
                <div class="card-body items-center text-center">
                    <p class="text-sm font-semibold uppercase tracking-widest text-primary">Erreur HTTP</p>
                    <h1 class="text-5xl font-black text-primary">{{ status }}</h1>
                    <h2 class="text-2xl font-bold">{{ title }}</h2>
                    <p class="max-w-2xl text-base-content/80">{{ description }}</p>
                    <p v-if="hint" class="alert alert-info mt-2 max-w-2xl text-sm">
                        {{ hint }}
                    </p>

                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <Link class="btn btn-primary" href="/">Retour à l'accueil</Link>
                        <button v-if="canGoBack" class="btn btn-ghost" type="button" @click="goBack">
                            Revenir à la page précédente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
