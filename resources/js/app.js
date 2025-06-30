import "./bootstrap";

import { Editor } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import { Markdown } from "tiptap-markdown";

// Daftarkan komponen Alpine `tiptapEditor`
document.addEventListener("alpine:init", () => {
    Alpine.data("tiptapEditor", (config) => {
        let editor;

        return {
            state: config.state,
            init(element) {
                const _this = this;
                editor = new Editor({
                    element: element,
                    extensions: [
                        StarterKit.configure({
                            heading: {
                                levels: [2, 3, 4],
                            },
                        }),
                        Markdown,
                    ],
                    content: this.state,
                    onUpdate: ({ editor }) => {
                        this.state = editor.storage.markdown.getMarkdown();
                    },
                });
                this.$watch("state", (newState) => {
                    if (
                        editor &&
                        newState !== editor.storage.markdown.getMarkdown()
                    ) {
                        editor.commands.setContent(newState, false);
                    }
                });
            },
            isActive(type, opts = {}) {
                return editor.isActive(type, opts);
            },
            toggleBold() {
                return editor.chain().focus().toggleBold().run();
            },
            toggleItalic() {
                return editor.chain().focus().toggleItalic().run();
            },
            toggleH2() {
                return editor.chain().focus().toggleHeading({ level: 2 }).run();
            },
            toggleH3() {
                return editor.chain().focus().toggleHeading({ level: 3 }).run();
            },
            toggleH4() {
                return editor.chain().focus().toggleHeading({ level: 4 }).run();
            },
            toggleOrderedList() {
                return editor.chain().focus().toggleOrderedList().run();
            },
            toggleBulletList() {
                return editor.chain().focus().toggleBulletList().run();
            },
        };
    });
});
