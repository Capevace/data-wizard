<?php

namespace App\Magic\Prompts;


use App\Magic\Tools\OutputStepLabels;
use Mateffy\Magic\Chat\Messages\TextMessage;
use Mateffy\Magic\Chat\Prompt;
use Mateffy\Magic\Chat\Prompt\Role;
use Mateffy\Magic\Chat\ToolChoice;

class GenerateStepLabelsPrompt implements Prompt
{
    public function __construct(
        protected string $instructions,
        protected ?string $schema = null,
        protected bool $shouldForceFunction = true,
    ) {}

    public function system(): string
    {
        return <<<'PROMPT'
        <instructions>
        You are a UI and UX copywriting expert.
        Your goal is to compose multiple UI labels from a AI data extraction application interface.
        You a given a JSON schema that you need to generate UI labels for. This schema is what the extractor will output.
        The instructions may contain further information about the schema, which will be very important for the UI labels.
        There may also be some concrete UI instructions in there, that you need to follow.

        The application interface has 4 steps:
        1. Introduction: The initial screen for the user, introducing the application and the purpose of the data extraction.
        2. Bucket: The interface in which PDF, images and other files can be uploaded. These files will be pre-processed and adapted for the LLM. The instructions may mention what kind of files will be accepted, it might be helpful to mention it here.
        3. Extraction: The extraction screen. The user sees a generated UI that is also composed based on the JSON schema. Even arrays and objects are beautifully rendered. When the LLM is finished, the user can edit the data directly in the UI. They can also restart the extraction with a button. The continue button should contain something about data being correct. The default label is "Data is correct, continue!".
        4. Review: The user is shown a summary of the extracted data. The description should ask the user that by coninuing, they made sure that the data is correct. There are also download buttons for CSV, JSON etc. output, but these labels are fixed. The submit button will then redirect them to another application, if configured. This will send the extracted data to another system. If you can infer that this is the case, you may talk accordinly in the description on this page.

        Descriptions may contain HTML, but only <p>, <ul> and <li>, <strong>, <em> and <a> tags are allowed.

        The user message will include the language that the output should be in. Make sure that the text is in that language.
        </instructions>

        <output-example>
        {
            "introduction_view_heading": "Willkommen bei Data Wizard 🪄",
            "introduction_view_description": "<p>Data Wizard ist ein Tool, das es dir ermöglicht, strukturierte Daten aus PDFs, Bildern und anderen Dateien zu extrahieren.</p><p>Das Tool ist konfiguriert, um Daten in einer bestimmten Struktur zu extrahieren, die dann von anderen Anwendungen verwendet werden kann.</p><p>Um loszulegen, klicke auf Weiter und lade deine Dateien hoch.</p>",
            "introduction_view_next_button_label": "Weiter",
            "bucket_view_heading": "Lade deine Dateien hoch",
            "bucket_view_description": "<p>Lade die Dateien hoch, die du extrahieren möchtest. Du kannst Bilder und PDFs hochladen.</p><p>Wenn du eine Datei hochlädst, wird sie in einem Ordner angelegt. Wenn du mehrere Dateien hochlädst, werden sie in einem Ordner mit dem Namen der Datei angelegt.</p><p>Wenn du mehrere Dateien hochlädst, werden sie in einem Ordner mit dem Namen der Datei angelegt.</p>",
            "bucket_view_back_button_label": "Warte, wie geht das nochmal?",
            "bucket_view_begin_button_label": "Ich bin bereit, loszulegen!",
            "bucket_view_continue_button_label": "Weiter",
            "extraction_view_heading": "Extrahieren",
            "extraction_view_description": "<p>Extrahiere Daten aus deinen Dateien.</p><p>Wenn du mehrere Dateien hochlädst, werden sie in einem Ordner mit dem Namen der Datei angelegt.</p>",
            "extraction_view_back_button_label": "Zurück",
            "extraction_view_continue_button_label": "Daten korrekt, weiter!",
            "extraction_view_restart_button_label": "Neu starten",
            "results_view_heading": "Ergebnisse",
            "results_view_description": "<p>Sehe dir deine extrahierten Daten an.</p><p>Wenn du mehrere Dateien hochlädst, werden sie in einem Ordner mit dem Namen der Datei angelegt.</p>",
            "results_view_back_button_label": "Zurück",
            "results_view_next_button_label": "Weiter"
        }
        </output-example>
        PROMPT;
    }

    public function prompt(): string
    {
        return <<<TXT
        <task>Generate UI labels for the given instructions and schema.</task>

        <data-schema>
        {$this->schema}
        </data-schema>

        <data-instructions>
        {$this->instructions}
        </data-instructions>
        TXT;
    }

    public function messages(): array
    {
        return [
            new TextMessage(role: Role::User, content: $this->prompt()),
        ];
    }

    public function tools(): array
    {
        return [
            new OutputStepLabels()
        ];
    }

    public function shouldParseJson(): bool
    {
        return true;
    }
    public function toolChoice(): ToolChoice|string
    {
        return (new OutputStepLabels)->name();
    }
}
