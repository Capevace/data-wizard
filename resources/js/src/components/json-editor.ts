// Configures two JSON schemas, with references.
import type { Alpine } from 'alpinejs';


import {EditorView, basicSetup} from "codemirror";
import {json} from "@codemirror/lang-json";

// import { basicSetup } from "codemirror";
import { EditorState, Compartment } from '@codemirror/state';
// import { EditorView, keymap } from "@codemirror/view";
// import { indentWithTab } from "@codemirror/commands";
// import { json } from "@codemirror/lang-json";
// import { Compartment } from '@codemirror/state';
// import { json } from "@codemirror/lang-json";
// import { php } from "@codemirror/lang-php";
// import { css } from "@codemirror/lang-css";
// import { html } from "@codemirror/lang-html";
import { materialLight as light } from "./json-editor/light";
import { materialDark as dark } from "./json-editor/dark";
import { basicLight } from 'cm6-theme-basic-light'
// import { basicDark } from 'cm6-theme-basic-dark'
// import { solarizedDark } from 'cm6-theme-solarized-dark'
// import { solarizedLight } from 'cm6-theme-solarized-light'
// import { materialDark } from 'cm6-theme-material-dark'
// import { nord as dark } from 'cm6-theme-nord'
// import { gruvboxLight } from 'cm6-theme-gruvbox-light'
// import { gruvboxDark } from 'cm6-theme-gruvbox-dark'
// import { indentWithTab } from "@codemirror/commands";
// import { json } from "@codemirror/lang-json";


// const jsonCode = ["{", '    "p1": "v3",', '    "p2": false', "}"].join("\n");
// const modelUri = monaco.Uri.parse("a://b/foo.json"); // a made up unique URI for our model
// const model = monaco.editor.createModel(jsonCode, "json", modelUri);
//
// // configure the JSON language support with schemas and schema associations
// monaco.languages.json.jsonDefaults.setDiagnosticsOptions({
// 	validate: true,
// 	schemas: [
// 		{
// 			uri: "http://myserver/foo-schema.json", // id of the first schema
// 			fileMatch: [modelUri.toString()], // associate with our model
// 			schema: {
// 				type: "object",
// 				properties: {
// 					p1: {
// 						enum: ["v1", "v2"],
// 					},
// 					p2: {
// 						$ref: "http://myserver/bar-schema.json", // reference the second schema
// 					},
// 				},
// 			},
// 		},
// 		{
// 			uri: "http://myserver/bar-schema.json", // id of the second schema
// 			schema: {
// 				type: "object",
// 				properties: {
// 					q1: {
// 						enum: ["x1", "x2"],
// 					},
// 				},
// 			},
// 		},
// 	],
// });
//
//


export function JsonEditorComponent({ statePath, state }) {
    return {
        state,
        editor: null,
        statePath,
        compartment: null,

        toggleTheme(theme) {
            const of = theme === 'dark'
                ? { extension: dark }
                : { extension: light };

            this.editor.dispatch({
                effects: this.compartment.reconfigure(of)
            });
        },

        init() {
            let timeout = null;

            this.compartment = new Compartment();

            const extensions = [
                basicSetup,
                json(),
                this.compartment.of({
                    extension: window.Alpine.store('theme') === 'dark' ? dark : light
                }),
                EditorView.updateListener.of((v) => {
                    if (v.docChanged) {
                        this.state = v.state.doc.toString();

                        if (timeout) {
                            clearTimeout(timeout);
                        }

                        timeout = setTimeout(() => {
                            this.$wire.$set(this.statePath, this.state);
                        }, 1000);
                    }


                }),
                EditorState.readOnly.of(this.isReadOnly)
            ];

            const startState = EditorState.create({
              doc: this.$wire.$get(this.statePath),
              extensions
            });

            this.editor = new EditorView({
                state: startState,
                extensions,
                parent: this.$refs.editor,
            })
        },

        destroy() {
            this.editor?.destroy();
        },
    };
}

export default function JsonEditorPlugin(Alpine: Alpine) {
    Alpine.data('JsonEditorComponent', JsonEditorComponent);
};
