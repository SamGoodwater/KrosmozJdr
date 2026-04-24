import StarterKit from "@tiptap/starter-kit";
import Link from "@tiptap/extension-link";
import Image from "@tiptap/extension-image";
import TextAlign from "@tiptap/extension-text-align";
import Highlight from "@tiptap/extension-highlight";
import Color from "@tiptap/extension-color";
import Underline from "@tiptap/extension-underline";
import Subscript from "@tiptap/extension-subscript";
import Superscript from "@tiptap/extension-superscript";
import Table from "@tiptap/extension-table";
import TableRow from "@tiptap/extension-table-row";
import TableCell from "@tiptap/extension-table-cell";
import TableHeader from "@tiptap/extension-table-header";
import TaskList from "@tiptap/extension-task-list";
import TaskItem from "@tiptap/extension-task-item";
import Placeholder from "@tiptap/extension-placeholder";
import CharacterCount from "@tiptap/extension-character-count";
import Focus from "@tiptap/extension-focus";
import { ReferenceInline } from "@/Composables/richText/ReferenceInlineExtension";

/**
 * Extensions TipTap communes (édition et lecture).
 *
 * @param {Object} opts
 * @param {string} [opts.placeholder]
 * @param {number|null} [opts.maxCharacters]
 * @param {boolean} [opts.enableReferenceInline]
 * @returns {import('@tiptap/core').Extension[]}
 */
export function createRichTextExtensions(opts = {}) {
    const placeholder = opts.placeholder ?? "Commencez à écrire...";
    const maxCharacters = opts.maxCharacters ?? null;
    const enableReferenceInline = Boolean(opts.enableReferenceInline);

    const extensions = [
        StarterKit.configure({
            heading: {
                levels: [1, 2, 3, 4, 5, 6],
            },
        }),
        Underline,
        Subscript,
        Superscript,
        Color,
        Highlight.configure({
            multicolor: true,
        }),
        TextAlign.configure({
            types: ["heading", "paragraph"],
            defaultAlignment: "left",
        }),
        Link.configure({
            openOnClick: false,
            linkOnPaste: true,
            HTMLAttributes: {
                class: "text-primary underline",
            },
        }),
        Image.configure({
            inline: true,
            allowBase64: true,
            HTMLAttributes: {
                class: "max-w-full h-auto rounded",
            },
        }),
        Table.configure({
            resizable: true,
            HTMLAttributes: {
                class: "border-collapse border border-base-300",
            },
        }),
        TableRow,
        TableHeader,
        TableCell,
        TaskList,
        TaskItem.configure({
            nested: true,
        }),
        Placeholder.configure({
            placeholder,
        }),
        CharacterCount.configure({
            limit: maxCharacters || undefined,
        }),
        Focus.configure({
            className: "has-focus",
            mode: "all",
        }),
    ];

    if (enableReferenceInline) {
        extensions.push(ReferenceInline);
    }

    return extensions;
}
