<script setup>
/**
 * Champ texte avec autocomplétion des clés de caractéristiques.
 * — Focus / bouton : ouvre la liste (top suggestions, filtrable).
 * — Frappe : filtre sur l’identifiant en cours (accepte `clé:caster` / `clé:target`).
 * — Clavier : ↑/↓, Entrée ou → pour valider, Échap ferme.
 */
import { computed, nextTick, ref, watch } from "vue";
import {
    getActiveIdentifierBounds,
    filterCharacteristicSuggestions,
    buildInsertionForFormula,
} from "@/Utils/characteristic/formulaAutocomplete";

const props = defineProps({
    modelValue: { type: String, default: "" },
    /** Liste plate : { id: clé métier, name?, short_name? } */
    suggestions: { type: Array, default: () => [] },
    /** Longueur minimale du fragment pour filtrer (0 = liste dès le focus). */
    minQueryLength: { type: Number, default: 0 },
    /** Ouvre la liste au focus même sans jeton. */
    openOnFocus: { type: Boolean, default: true },
    /** Si true : insère [clé]. Si false : insère la clé seule. */
    useBrackets: { type: Boolean, default: true },
    placeholder: { type: String, default: "" },
    disabled: { type: Boolean, default: false },
    inputClass: {
        type: String,
        default: "input input-bordered input-sm w-full min-w-0 font-mono text-sm pr-9",
    },
});

const emit = defineEmits(["update:modelValue"]);

const rootEl = ref(null);
const inputEl = ref(null);
const open = ref(false);
const activeIndex = ref(0);
const caretPos = ref(0);
/** Mode « catalogue » : liste ouverte sans jeton (focus / bouton). */
const browseMode = ref(false);

const token = computed(() => {
    const { token: t } = getActiveIdentifierBounds(props.modelValue, caretPos.value);
    return t;
});

const filtered = computed(() => {
    const list = Array.isArray(props.suggestions) ? props.suggestions : [];
    const q = token.value;
    if (browseMode.value && q.length < props.minQueryLength) {
        return list.slice(0, 40);
    }
    return filterCharacteristicSuggestions(list, q, {
        minLength: props.minQueryLength,
        maxResults: 50,
    });
});

const showList = computed(() => open.value && filtered.value.length > 0);

watch(
    () => props.modelValue,
    () => {
        activeIndex.value = 0;
    },
);

watch(filtered, (list) => {
    if (!Array.isArray(list) || list.length === 0) {
        activeIndex.value = 0;
        return;
    }
    if (activeIndex.value >= list.length) activeIndex.value = 0;
});

function onInput(ev) {
    const el = ev.target;
    caretPos.value = el.selectionStart ?? 0;
    emit("update:modelValue", el.value);
    browseMode.value = false;
    open.value = true;
}

function syncCaretFromEvent(ev) {
    const pos = ev.target?.selectionStart;
    if (typeof pos === "number") caretPos.value = pos;
}

function onFocus(ev) {
    syncCaretFromEvent(ev);
    if (props.openOnFocus && !props.disabled) {
        browseMode.value = token.value.length < props.minQueryLength;
        open.value = true;
    }
}

function toggleBrowse() {
    if (props.disabled) return;
    if (open.value && browseMode.value) {
        open.value = false;
        browseMode.value = false;
        return;
    }
    browseMode.value = true;
    open.value = true;
    nextTick(() => inputEl.value?.focus());
}

function onKeydown(ev) {
    const el = ev.target;
    caretPos.value = el.selectionStart ?? 0;

    if (!showList.value) {
        if (ev.key === "Escape") {
            open.value = false;
            browseMode.value = false;
        }
        return;
    }

    if (ev.key === "ArrowDown") {
        ev.preventDefault();
        activeIndex.value = (activeIndex.value + 1) % filtered.value.length;
        return;
    }
    if (ev.key === "ArrowUp") {
        ev.preventDefault();
        activeIndex.value = (activeIndex.value - 1 + filtered.value.length) % filtered.value.length;
        return;
    }
    if (ev.key === "Enter" || ev.key === "ArrowRight") {
        ev.preventDefault();
        applyPick(filtered.value[activeIndex.value]);
        return;
    }
    if (ev.key === "Escape") {
        ev.preventDefault();
        open.value = false;
        browseMode.value = false;
    }
}

function applyPick(item) {
    if (!item?.id) return;
    const value = String(props.modelValue ?? "");
    const { start, end } = getActiveIdentifierBounds(value, caretPos.value);
    const key = String(item.id);
    let insertion;
    if (browseMode.value && start === end && (value === "" || /\s$/.test(value.slice(0, start)))) {
        insertion = props.useBrackets ? `[${key}]` : key;
        const next = value.slice(0, start) + insertion + value.slice(end);
        emit("update:modelValue", next);
        open.value = false;
        browseMode.value = false;
        const newCaret = start + insertion.length;
        nextTick(() => {
            const inp = inputEl.value;
            if (inp && typeof inp.setSelectionRange === "function") {
                inp.focus();
                inp.setSelectionRange(newCaret, newCaret);
                caretPos.value = newCaret;
            }
        });
        return;
    }
    insertion = buildInsertionForFormula(value, start, end, key, props.useBrackets);
    const next = value.slice(0, start) + insertion + value.slice(end);
    emit("update:modelValue", next);
    open.value = false;
    browseMode.value = false;
    const newCaret = start + insertion.length;
    nextTick(() => {
        const inp = inputEl.value;
        if (inp && typeof inp.setSelectionRange === "function") {
            inp.focus();
            inp.setSelectionRange(newCaret, newCaret);
            caretPos.value = newCaret;
        }
    });
}

function onBlur() {
    setTimeout(() => {
        if (!rootEl.value?.contains(document.activeElement)) {
            open.value = false;
            browseMode.value = false;
        }
    }, 0);
}

function onSuggestionMouseDown(ev) {
    ev.preventDefault();
}
</script>

<template>
    <div ref="rootEl" class="relative w-full min-w-0">
        <input
            ref="inputEl"
            type="text"
            :value="modelValue"
            :disabled="disabled"
            :class="inputClass"
            :placeholder="placeholder"
            autocomplete="off"
            spellcheck="false"
            @input="onInput"
            @click="syncCaretFromEvent"
            @keyup="syncCaretFromEvent"
            @focus="onFocus"
            @keydown="onKeydown"
            @blur="onBlur"
        />
        <button
            type="button"
            class="absolute right-1 top-1/2 -translate-y-1/2 btn btn-ghost btn-xs btn-square opacity-70 hover:opacity-100"
            :disabled="disabled"
            title="Liste des caractéristiques"
            tabindex="-1"
            @mousedown.prevent="toggleBrowse"
        >
            ▾
        </button>
        <ul
            v-if="showList"
            class="absolute z-50 mt-1 max-h-60 w-full min-w-[16rem] overflow-auto rounded-box border border-base-300 bg-base-100 py-1 text-sm shadow-lg"
            role="listbox"
        >
            <li
                v-for="(item, idx) in filtered"
                :key="item.id + '-' + idx"
                role="option"
                :aria-selected="idx === activeIndex"
                class="cursor-pointer px-3 py-1.5 hover:bg-base-200"
                :class="{ 'bg-primary/15': idx === activeIndex }"
                @mousedown="onSuggestionMouseDown"
                @click="applyPick(item)"
            >
                <span class="font-mono text-xs text-primary">{{ item.id }}</span>
                <span v-if="item.name" class="ml-2 text-base-content/80">{{ item.name }}</span>
            </li>
        </ul>
    </div>
</template>
