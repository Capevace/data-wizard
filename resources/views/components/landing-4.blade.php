<div class="bg-[#0A0F1A] text-gray-200 font-sans">

    {{-- Hero Section --}}
    <x-landing.hero />

    {{-- What is Data Wizard Section --}}
    <div class="py-24 relative">
      <div class="absolute inset-0 bg-gradient-to-b from-black/10 to-black/30 backdrop-blur-sm"></div>
        <div class="relative z-10 container mx-auto px-6">
            <h2 class="text-4xl font-bold text-center mb-16 text-gray-100 text-shadow-md">What is Data Wizard?</h2>
            <div class="grid lg:grid-cols-2 gap-16 items-center"> {{-- items-stretch might be needed if content differs vastly --}}
                <div class="text-lg text-gray-300 space-y-6">
                    <p>
                        Data Wizard is an open-source tool that uses Large Language Models to pull structured data from various documents – <strong class="what-is-strong">regardless of their size or format</strong>.
                    </p>
                    <p>
                        Simply define your desired output using <strong class="what-is-strong">JSON Schema</strong>, pick an <strong class="what-is-strong">Extraction Strategy</strong>, choose your <strong class="what-is-strong">LLM</strong> (like OpenAI, Anthropic, or local models), and let Data Wizard extract and validate the data.
                    </p>
                    <p>
                        Ideal for automating data entry or adding smart import features to your apps. Integrate via <strong class="what-is-strong">iFrame</strong> or use programmatically with the <strong class="what-is-strong">REST/GraphQL API</strong>.
                    </p>
                </div>
                 {{-- Diagram with always-on gradient shadow --}}
                <div class="overflow-hidden text-center p-6 !rounded-2xl bg-black/20 backdrop-blur-lg border border-white/10 shadow-xl gradient-shadow-always-on">
                    <x-diagrams.overview class="w-full h-auto max-w-md mx-auto"/>
                </div>
            </div>
        </div>
    </div>

    <x-landing.cta-inline />

    <x-landing.key-features />

    {{-- Add Keyframes and Styles for Progress Bar Animation & Pause --}}
    <style>
        @keyframes progress-bar {
            from { width: 0%; }
            to { width: 100%; }
        }
        .animate-progress {
            animation: progress-bar linear forwards;
            /* animation-play-state: running; */ /* Default state */
        }
        .animation-paused {
            animation-play-state: paused;
        }
        /* Ensure only one feature content is interactive at a time during transition */
        .feature-content[x-transition\\:leave] {
            pointer-events: none;
        }
    </style>
</div>

    {{-- Getting Started Section --}}
    <div id="how-to-use" class="container mx-auto px-6 py-24">
        <h2 class="text-4xl font-bold text-center mb-16 text-gray-100 text-shadow-md">How it works</h2>
        {{-- Use grid and ensure direct children are the cards for h-full --}}
        <div class="grid md:grid-cols-3 gap-8">
             {{-- Step 1 --}}
             <div class="group">
                 {{-- Added !p-8 for padding, glass-card has h-full --}}
                 <div class="glass-card !p-8 gradient-shadow-card">
                     <div class="font-bold text-xl aspect-square w-10 h-10 absolute -top-4 -left-4 flex flex-col items-center justify-center bg-gradient-to-br from-cyan-500/90 to-purple-600/90 rounded-lg backdrop-blur-sm">
                         1.
                     </div>
                     <h3 class="text-xl font-semibold mb-4 text-white">Configure Extractor</h3>
                     <p class="text-gray-300"> Define the data structure you need with JSON Schema. Select an LLM and an extraction strategy.</p>
                 </div>
             </div>
             {{-- Step 2 --}}
             <div class="group">
                 <div class="glass-card !p-8 gradient-shadow-card">
                     <div class="font-bold text-xl aspect-square w-10 h-10 absolute -top-4 -left-4 flex flex-col items-center justify-center bg-gradient-to-br from-cyan-500/90 to-purple-600/90 rounded-lg backdrop-blur-sm">
                         2.
                     </div>
                     <h3 class="text-xl font-semibold mb-4 text-white">Upload Documents</h3>
                     <p class="text-gray-300"> Upload your files via UI, iFrame, or REST API. Supports PDFs, Word/Excel documents, images, and more.</p>
                 </div>
             </div>
             {{-- Step 3 --}}
             <div class="group">
                 <div class="glass-card !p-8 gradient-shadow-card">
                     <div class="font-bold text-xl aspect-square w-10 h-10 absolute -top-4 -left-4 flex flex-col items-center justify-center bg-gradient-to-br from-cyan-500/90 to-purple-600/90 rounded-lg backdrop-blur-sm">
                         3.
                     </div>
                     <h3 class="text-xl font-semibold mb-4 text-white">Get Structured Data</h3>
                     <p class="text-gray-300"> AI extracts data & validates it against the schema. Receive clean JSON via UI, webhook, or API. </p>
                 </div>
             </div>
        </div>
    </div>

    {{-- Strategies Section --}}
    <div id="strategies" class="container mx-auto px-6 py-24">
        <h2 class="text-4xl font-bold text-center mb-6 text-gray-100 text-shadow-md">Extraction Strategies For Any Document</h2>
        <p class="text-center text-lg text-gray-300 mb-20 max-w-3xl mx-auto text-shadow">
            Choose the best strategy for your document type and complexity. Strategies control how documents are processed and sent to the LLM.
        </p>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Strategy Cards using glass-card (has h-full) --}}
             <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-file" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Simple</h3> <p class="text-gray-300 text-sm">Sends the entire document (or as much as fits) to the LLM in one go. Best for small documents.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-arrow-right-left" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Sequential</h3> <p class="text-gray-300 text-sm">Processes document chunks one by one, feeding results from the previous chunk into the next prompt. Maintains context.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-git-fork" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Parallel</h3> <p class="text-gray-300 text-sm">Processes document chunks independently and simultaneously. Good for documents with unrelated data points across pages.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-merge" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Auto-Merging</h3> <p class="text-gray-300 text-sm">Extends Sequential or Parallel. Concatenates results from chunks and runs a final LLM call to deduplicate. Helps prevent lost items.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-repeat-2" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Double-Pass</h3> <p class="text-gray-300 text-sm">First pass uses Parallel, second pass uses Sequential to review and refine. Combines speed and accuracy. Supports auto-merging.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-flask-conical" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Custom Strategy</h3> <p class="text-gray-300 text-sm">Build your own strategy in PHP by implementing an interface. Add custom logic, validation, or API calls.</p> </div> </div>
        </div>
    </div>

    <x-landing.cta-inline />


    {{-- How It Works Section --}}
    <div id="how-it-works-tech" class="py-24 relative">
         <div class="absolute inset-0 bg-gradient-to-b from-black/50 to-black/30 backdrop-blur-sm"></div>
        <div class="absolute bottom-1/4 right-10 w-72 h-72 bg-gradient-radial from-fuchsia-600/20 to-transparent blur-3xl -z-10 animate-pulse"></div>
        <div class="relative z-10 container mx-auto px-6">
             <h2 class="text-4xl font-bold text-center mb-16 text-gray-100 text-shadow-md">Under the Hood: The Process</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- Process Steps using glass-card (has h-full) --}}
                <div class="group"> <div class="flex flex-col items-center text-center glass-card !p-8 gradient-shadow-card"> <x-icon name="lucide-file-scan" class="h-10 w-10 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">1. Pre-processing</h3> <p class="text-gray-300 text-sm"> File contents are extracted and pre-processed. Text and images are separated, document page screenshots are taken.</p> </div> </div>
                <div class="group"> <div class="flex flex-col items-center text-center glass-card !p-8 gradient-shadow-card"> <x-icon name="lucide-list-tree" class="h-10 w-10 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">2. Strategy & Schema</h3> <p class="text-gray-300 text-sm"> The strategy determines how to chunk your document and merge the data. The schema defines the output structure. </p> </div> </div>
                <div class="group"> <div class="flex flex-col items-center text-center glass-card !p-8 gradient-shadow-card"> <x-icon name="lucide-brain-circuit" class="h-10 w-10 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">3. LLM Interaction</h3> <p class="text-gray-300 text-sm"> Document chunks are sent to the LLM until the full document is processed. The LLM generates JSON output. </p> </div> </div>
                <div class="group"> <div class="flex flex-col items-center text-center glass-card !p-8 gradient-shadow-card"> <x-icon name="lucide-check-check" class="h-10 w-10 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">4. Validation & Output</h3> <p class="text-gray-300 text-sm"> LLM output is validated against the schema. The LLM fixes its own mistakes. The final JSON is returned to the user. </p> </div> </div>
            </div>
        </div>
    </div>

    {{-- Screenshots Section --}}
<div id="screenshots" class="py-24 relative overflow-hidden" x-data>
    {{-- Background overlay & subtle glow --}}
    <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/60 to-black/40 backdrop-blur-sm"></div>
    <div class="absolute -bottom-40 -left-40 w-[35rem] h-[35rem] bg-gradient-radial from-cyan-700/15 to-transparent blur-3xl -z-10 animate-pulse" style="animation-delay: 0.5s;"></div>
    <div class="absolute -top-40 -right-40 w-[35rem] h-[35rem] bg-gradient-radial from-purple-700/15 to-transparent blur-3xl -z-10 animate-pulse" style="animation-delay: 2s;"></div>

    <div class="relative z-10 container mx-auto px-6">
        <h2 class="text-4xl font-bold text-center mb-6 text-gray-100 text-shadow-md">See Data Wizard in Action</h2>
        <p class="text-center text-lg text-gray-300 mb-16 max-w-3xl mx-auto text-shadow">
            Get a glimpse of the intuitive interface and powerful features that make data extraction effortless.
        </p>

        <div class="grid md:grid-cols-2 gap-12 lg:gap-16 items-start">

            <x-landing.screenshot
                :src="url('images/screenshots/run/run-gui.png')"
                alt="Screenshot of the backend run UI"
                title="Standalone UI for running and evaluating extractors"
            >
                The standalone UI allows you to run extraction tasks manually, which also helps evaluating and debugging your extractor.
            </x-landing.screenshot>

            <x-landing.screenshot
                :src="url('images/screenshots/setup/edit-extractor.png')"
                alt="Screenshot of the extractor editor UI"
                title="Reusable Extractors for different documents"
            >
                Create reusable extractors for different documents. The built-in extractor editor allows you to define the JSON Schema, configure extra instructions & the context window, as well as the extraction strategy.
            </x-landing.screenshot>

            <x-landing.screenshot
                :src="url('images/screenshots/embedded-light-upload-done.png')"
                alt="Screenshot of the file uploading UI"
                title="Uploaded files are processed in the background"
            >
                Users can upload files via the UI. The files are pre-processed in the background, with the text and any embedded images being extracted from the PDF or Word file.
            </x-landing.screenshot>

            <x-landing.screenshot
                :src="url('images/screenshots/embedded-light-editing.png')"
                alt="Screenshot of the generated UI based on JSON Schema"
                title="Generated UI based on JSON Schema"
            >
                Easily embed Data Wizard in your app. Users can upload documents, edit JSON, and view results in a user-friendly interface.
            </x-landing.screenshot>

            <x-landing.screenshot
                :src="url('images/screenshots/embedded-light-card-error.png')"
                alt="Screenshot of an error in the generated UI"
                title="Data is validated against the JSON Schema"
            >
                The JSON output is validated against the JSON Schema, including rules like <code>minLength</code> or <code>multipleOf</code>.
            </x-landing.screenshot>


            <x-landing.screenshot
                :src="url('images/screenshots/setup/select-model.png')"
                alt="Screenshot of the model selector showing lots of LLMs"
                title="Select from a variety of LLMs"
            >
                Data Wizard is not limited to a single LLM provider. You can choose from a variety of LLMs, including GPT-4, Claude and Gemini.
            </x-landing.screenshot>
        </div>
    </div>
</div>

    <x-landing.cta-inline />

    {{-- Use Cases Section --}}
    <div class="container mx-auto px-6 py-24">
         <h2 class="text-4xl font-bold text-center mb-16 text-gray-100 text-shadow-md">Example Use Cases</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
             {{-- Use Case Cards using glass-card (has h-full) --}}
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-file-input" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Automate Data Entry</h3> <p class="text-gray-300 text-sm">Extract from invoices, receipts, forms into ERP/accounting systems.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-upload" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">SaaS Smart Import</h3> <p class="text-gray-300 text-sm">Let users upload documents to populate data in your CRM or SaaS app.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-database" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Document Conversion</h3> <p class="text-gray-300 text-sm">Turn document batches (PDFs, scans) into structured JSON/CSV.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-gem" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Core Extraction Engine</h3> <p class="text-gray-300 text-sm">Power your document processing platform with Data Wizard's API.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-bar-chart-3" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Market Research</h3> <p class="text-gray-300 text-sm">Gather product/pricing data from competitor brochures or websites.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-clipboard-check" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Compliance Checks</h3> <p class="text-gray-300 text-sm">Extract specific clauses or data points from contracts or reports.</p> </div> </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="container mx-auto px-6 py-12 border-t border-white/10 mt-16">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center space-x-2">
                <x-icon name="lucide-wand" class="h-6 w-6 text-cyan-400"/>
                <span class="text-xl font-bold text-gray-200">Data Wizard</span>
            </div>
            <div class="flex items-center space-x-6 text-sm text-gray-400">
                <a href="{{ url('/legal') }}" class="hover:text-cyan-300 transition-colors">Imprint / Legal information</a>
                <a href="{{ config('landing.license-url') }}" class="hover:text-cyan-300 transition-colors">License</a>
            </div>
             <div class="text-sm text-gray-500 mt-4 md:mt-0">
                 © {{ date('Y') }}
                 <a href="https://mateffy.me">Lukas Mateffy</a>.
                 Data Wizard is AGPL-3.0 licensed.
             </div>
        </div>
    </footer>
</div>
