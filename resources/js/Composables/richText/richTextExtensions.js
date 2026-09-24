import StarterKit from "@tiptap/starter-kit";
import Image from "@tiptap/extension-image";
import TextAlign from "@tiptap/extension-text-align";
import Highlight from "@tiptap/extension-highlight";
import { TextStyle } from "@tiptap/extension-text-style";
import { Color } from "@tiptap/extension-color";
import Subscript from "@tiptap/extension-subscript";
import Superscript from "@tiptap/extension-superscript";
import { TableKit } from "@tiptap/extension-table";
import { TaskList, TaskItem } from "@tiptap/extension-list";
import { Placeholder, CharacterCount, Focus } from "@tiptap/extensions";
import { ReferenceInline } from "@/Composables/richText/ReferenceInlineExtension";

/**
 * Extensions TipTap communes (édition et lecture).
 *
 * TipTap 3 : StarterKit embarque Link + Underline ; tables via `TableKit`
 * (Table + Row + Header + Cell) ; listes/utils via `@tiptap/extension-list|extensions`.
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
            link: {
                openOnClick: false,
                linkOnPaste: true,
                HTMLAttributes: {
                    class: "text-primary underline",
                },
            },
        }),
        TextStyle,
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
        Image.configure({
            inline: true,
            allowBase64: true,
            HTMLAttributes: {
                class: "max-w-full h-auto rounded",
            },
        }),
        TableKit.configure({
            table: {
                resizable: true,
                HTMLAttributes: {
                    class: "border-collapse border border-base-300",
                },
            },
        }),
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
