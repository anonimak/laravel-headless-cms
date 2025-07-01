import "./bootstrap";

import { Editor } from "@tiptap/core";
import Link from "@tiptap/extension-link";
import Image from "@tiptap/extension-image";
import StarterKit from "@tiptap/starter-kit";
import { Markdown } from "tiptap-markdown";
import Dropcursor from "@tiptap/extension-dropcursor";

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
                        Link.configure({
                            openOnClick: true,
                            defaultProtocol: "https",
                        }),
                        Image.configure({
                            inline: true,
                            allowBase64: true,
                        }),
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
            isLoaded() {
                return editor && editor.isMounted;
            },
            destroy() {
                if (editor) {
                    editor.destroy();
                    editor = null;
                }
            },
            isActive(type, opts = {}) {
                return editor.isActive(type, opts);
            },
            toggleParagraph() {
                return editor.chain().focus().setParagraph().run();
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
            setLink() {
                const previousUrl = editor.getAttributes("link").href;
                const url = window.prompt("URL", previousUrl);

                // cancelled
                if (url === null) {
                    return;
                }

                // empty
                if (url === "") {
                    editor
                        .chain()
                        .focus()
                        .extendMarkRange("link")
                        .unsetLink()
                        .run();

                    return;
                }
                // update link
                editor
                    .chain()
                    .focus()
                    .extendMarkRange("link")
                    .setLink({ href: url })
                    .run();
            },
            toggleCode() {
                return editor.chain().focus().toggleCode().run();
            },
            addImage() {
                const url = window.prompt("URL");

                if (url) {
                    editor.chain().focus().setImage({ src: url }).run();
                }
            },
        };
    });
});
