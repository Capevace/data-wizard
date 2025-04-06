{{-- Main Background Gradient & Settings --}}
<div class="bg-[#0A0F1A] text-gray-200 font-sans">

    {{-- Hero Section --}}
    <div class="relative overflow-hidden pb-16 pt-6">
        {{-- Background elements & Wizard Image --}}
        <div class="absolute -top-40 -left-40 w-[40rem] h-[40rem] bg-gradient-radial from-cyan-500/20 via-purple-500/10 to-transparent blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-60 -right-20 w-[30rem] h-[30rem] bg-gradient-radial from-fuchsia-500/15 via-blue-500/5 to-transparent blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>
        <div class="absolute inset-0 bg-[url('/path/to/subtle-grid-pattern.svg')] opacity-[0.02]"></div>
        <img src="/placeholder-wizard.png" alt="Data Wizard Mascot" class="absolute top-0 right-0 w-[300px] h-auto opacity-20 -z-0 pointer-events-none transform translate-x-1/4 -translate-y-1/4 blur-[2px] hidden lg:block" aria-hidden="true" />

        {{-- Navigation --}}
        <nav class="relative z-10 container mx-auto px-6 py-6">
            {{-- Nav content ... --}}
             <div class="flex items-center justify-between">
                <a href="/" class="flex items-center space-x-2 group">
                    <x-icon name="lucide-wand" class="h-8 w-8 text-cyan-400 group-hover:text-white transition-colors duration-300 group-hover:animate-spin" style="animation-duration: 1.5s;"/>
                    <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-cyan-300 to-fuchsia-400">
                        Data Wizard
                    </span>
                </a>
                <div class="flex items-center space-x-6">
                    <a href="https://github.com/mateffy/data-wizard" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-2 text-gray-300 hover:text-cyan-300 transition-colors">
                        <x-icon name="lucide-github" class="h-5 w-5"/>
                        <span>GitHub</span>
                    </a>
                    <a href="/docs" class="flex items-center space-x-2 text-gray-300 hover:text-cyan-300 transition-colors">
                        <x-icon name="lucide-file-text" class="h-5 w-5"/>
                        <span>Docs</span>
                    </a>
                </div>
            </div>
        </nav>

        {{-- Hero Content --}}
        <div class="relative z-10 container mx-auto px-6 pt-16 pb-24 text-center">
           {{-- Hero content ... --}}
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-center mb-6">
                    <x-icon name="lucide-sparkles" class="h-12 w-12 text-cyan-400 animate-pulse"/>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-white via-cyan-300 to-fuchsia-400 text-shadow-md">
                    Extract Structured Data from Any Document
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-10 text-shadow">
                    Turn documents like PDFs, Word files, and images into structured, validated JSON using AI. Open-source and easy to integrate.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="#how-to-use" class="px-8 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-lg font-semibold hover:from-cyan-400 hover:to-blue-500 transition-all duration-300 shadow-lg transform hover:scale-105">
                        Get Started
                    </a>
                    <a href="/docs" class="px-8 py-3 bg-white/10 rounded-lg font-semibold backdrop-blur-sm hover:bg-white/20 transition-colors border border-white/20 transform hover:scale-105">
                        View Documentation
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- What is Data Wizard Section --}}
    <div class="py-24 relative">
      {{-- Overlay & Content... --}}
      <div class="absolute inset-0 bg-gradient-to-b from-black/10 to-black/30 backdrop-blur-sm"></div>
        <div class="relative z-10 container mx-auto px-6">
            <h2 class="text-4xl font-bold text-center mb-16 text-gray-100 text-shadow-md">What is Data Wizard?</h2>
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div class="text-lg text-gray-300 space-y-6">
                    <p> Data Wizard is an open-source tool that uses Large Language Models (LLMs) to pull structured data from various documents. </p>
                    <p> Simply define your desired output using <strong>JSON Schema</strong>, pick an <strong>Extraction Strategy</strong>, choose your <strong>LLM</strong> (like OpenAI, Anthropic, or local models), and let Data Wizard extract and validate the data. </p>
                    <p> Ideal for automating data entry or adding smart import features to your apps. Integrate via <strong>iFrame</strong> or programmatically with the <strong>REST/GraphQL API</strong>. </p>
                </div>
                <div class="text-center p-6 rounded-2xl bg-black/20 backdrop-blur-lg border border-white/10 shadow-xl gradient-shadow-always-on">
                    <x-diagrams.overview class="w-full h-auto max-w-md mx-auto"/>
                    <p class="text-sm text-gray-400 mt-4">Core Extraction Flow</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Getting Started Section --}}
    <div id="how-to-use" class="container mx-auto px-6 py-24">
        <h2 class="text-4xl font-bold text-center mb-16 text-gray-100 text-shadow-md">Getting Started</h2>
        <div class="grid md:grid-cols-3 gap-8">
             {{-- Steps 1, 2, 3 using glass-card and gradient-shadow-card --}}
             <div class="group"> <div class="glass-card !p-8 gradient-shadow-card"> <div class="absolute -top-4 -left-4 p-3 bg-gradient-to-br from-cyan-500/30 to-purple-600/30 rounded-lg backdrop-blur-sm"><x-icon name="lucide-settings-2" class="h-6 w-6 text-cyan-300"/></div> <h3 class="text-xl font-semibold mb-4 text-white">1. Configure Extractor</h3> <p class="text-gray-300"> Define the data structure you need with JSON Schema. Select an LLM, strategy, and instructions. </p> </div> </div>
             <div class="group"> <div class="glass-card !p-8 gradient-shadow-card"> <div class="absolute -top-4 -left-4 p-3 bg-gradient-to-br from-cyan-500/30 to-purple-600/30 rounded-lg backdrop-blur-sm"><x-icon name="lucide-file-up" class="h-6 w-6 text-cyan-300"/></div> <h3 class="text-xl font-semibold mb-4 text-white">2. Upload Documents</h3> <p class="text-gray-300"> Upload files (PDFs, images, etc.) via UI, iFrame, or REST API. </p> </div> </div>
             <div class="group"> <div class="glass-card !p-8 gradient-shadow-card"> <div class="absolute -top-4 -left-4 p-3 bg-gradient-to-br from-cyan-500/30 to-purple-600/30 rounded-lg backdrop-blur-sm"><x-icon name="lucide-check-check" class="h-6 w-6 text-cyan-300"/></div> <h3 class="text-xl font-semibold mb-4 text-white">3. Get Structured Data</h3> <p class="text-gray-300"> AI extracts & validates against schema. Receive clean JSON via UI, webhook, or API. </p> </div> </div>
        </div>
    </div>

     {{-- Key Features Section --}}
    <div id="features" class="py-24 relative">
        {{-- Background overlay & glow --}}
        <div class="absolute inset-0 bg-gradient-to-b from-black/30 to-black/50 backdrop-blur-sm"></div>
        <div class="absolute top-1/4 left-10 w-72 h-72 bg-gradient-radial from-blue-600/30 to-transparent blur-3xl -z-10 animate-pulse"></div>
        <div class="relative z-10 container mx-auto px-6">
            <h2 class="text-4xl font-bold text-center mb-16 text-gray-100 text-shadow-md">Key Features</h2>
            <div class="grid md:grid-cols-2 gap-x-12 gap-y-8 items-start">
                {{-- Left Column - Feature List (Now Boxed) --}}
                <div class="space-y-8">
                    {{-- Feature 1 - Boxed --}}
                     <div class="group">
                        <div class="feature-card-box gradient-shadow-card"> {{-- Use new box class --}}
                             <div class="flex items-start space-x-4 scale-on-hover"> {{-- Add inner flex container + scale target --}}
                                <div class="icon-bg"> <x-icon name="lucide-puzzle" class="h-6 w-6 icon-color"/> </div>
                                <div>
                                    <h3 class="text-xl font-semibold mb-2 text-white">Flexible Extraction Engine</h3>
                                    <p class="text-gray-300 text-sm">Use JSON Schema for precise output. Choose LLMs (OpenAI, Anthropic, local) & strategies per task.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Feature 2 - Boxed --}}
                     <div class="group">
                         <div class="feature-card-box gradient-shadow-card">
                            <div class="flex items-start space-x-4 scale-on-hover">
                                <div class="icon-bg"> <x-icon name="lucide-code" class="h-6 w-6 icon-color"/> </div>
                                <div>
                                    <h3 class="text-xl font-semibold mb-2 text-white">Seamless Integration</h3>
                                    <p class="text-gray-300 text-sm">Embed via iFrame (with JS API) or use REST/GraphQL APIs. Get updates via Webhooks.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Feature 3 - Boxed --}}
                    <div class="group">
                        <div class="feature-card-box gradient-shadow-card">
                           <div class="flex items-start space-x-4 scale-on-hover">
                                <div class="icon-bg"> <x-icon name="lucide-github" class="h-6 w-6 icon-color"/> </div>
                                <div>
                                    <h3 class="text-xl font-semibold mb-2 text-white">Open Source & Extensible</h3>
                                    <p class="text-gray-300 text-sm">Self-host with Docker (MIT License). Build custom PHP strategies for unique workflows.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Feature 4 - Boxed --}}
                    <div class="group">
                         <div class="feature-card-box gradient-shadow-card">
                            <div class="flex items-start space-x-4 scale-on-hover">
                                <div class="icon-bg"> <x-icon name="lucide-file-check-2" class="h-6 w-6 icon-color"/> </div>
                                <div>
                                    <h3 class="text-xl font-semibold mb-2 text-white">Robust Document Handling</h3>
                                    <p class="text-gray-300 text-sm">Processes PDFs, DOCX, images, etc. Includes OCR, image extraction & page screenshots for context.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column - Code Preview --}}
                <div class="sticky top-24">
                    <div class="relative p-6 rounded-xl bg-[#111827]/70 backdrop-blur-md overflow-hidden border border-white/10 shadow-xl gradient-shadow-always-on">
                         {{-- Code Preview Content ... --}}
                          <h3 class="text-xl font-semibold mb-4 text-white">Example: Invoice JSON Schema</h3>
                         <div class="max-h-96 overflow-y-auto rounded-md">
                            <pre class="text-xs text-gray-200 overflow-x-auto bg-[#0d1117] p-4 rounded-md"><code class="language-json">{
  "title": "Invoice",
  "description": "Schema for basic invoice data",
  "type": "object",
  "properties": {
    "invoiceNumber": { "type": "string", "description": "Unique invoice identifier" },
    "issueDate": { "type": "string", "format": "date" },
    "currency": { "type": "string", "enum": ["EUR", "USD"] },
    "seller": { "type": "object", "properties": { }, "required": ["name"] },
    "buyer": { },
    "lineItems": { "type": "array", "description": "List of invoice items", "magic_ui": "table", "items": { } },
    "totalAmounts": { }
  },
  "required": ["invoiceNumber", "issueDate", "seller", "lineItems", "totalAmounts"]
}</code></pre>
                        </div>
                        <div class="pt-4 mt-4 border-t border-white/10">
                            <p class="text-sm text-gray-300">
                                Define your desired JSON structure. Data Wizard uses it to guide the AI and validate the output.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Strategies Section --}}
    <div id="strategies" class="container mx-auto px-6 py-24">
       {{-- Heading & Paragraph ... --}}
        <h2 class="text-4xl font-bold text-center mb-16 text-gray-100 text-shadow-md">Flexible Extraction Strategies</h2>
        <p class="text-center text-lg text-gray-300 mb-12 max-w-3xl mx-auto text-shadow">
            Choose the best strategy for your document type and complexity. Strategies control how documents are processed and sent to the LLM.
        </p>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Strategy Cards ... --}}
             <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-file" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Simple</h3> <p class="text-gray-300 text-sm">Sends the entire document (or as much as fits) to the LLM in one go. Best for small documents.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-arrow-right-left" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Sequential</h3> <p class="text-gray-300 text-sm">Processes document chunks one by one, feeding results from the previous chunk into the next prompt. Maintains context.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-git-fork" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Parallel</h3> <p class="text-gray-300 text-sm">Processes document chunks independently and simultaneously. Good for documents with unrelated data points across pages.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-merge" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Auto-Merging</h3> <p class="text-gray-300 text-sm">Extends Sequential or Parallel. Concatenates results from chunks and runs a final LLM call to deduplicate. Helps prevent lost items.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-repeat-2" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Double-Pass</h3> <p class="text-gray-300 text-sm">First pass uses Parallel, second pass uses Sequential to review and refine. Combines speed and accuracy. Supports auto-merging.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-flask-conical" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Custom Strategy</h3> <p class="text-gray-300 text-sm">Build your own strategy in PHP by implementing an interface. Add custom logic, validation, or API calls.</p> </div> </div>
        </div>
    </div>


    {{-- How It Works Section --}}
    <div id="how-it-works-tech" class="py-24 relative">
        {{-- Background overlay & glow ... --}}
         <div class="absolute inset-0 bg-gradient-to-b from-black/50 to-black/30 backdrop-blur-sm"></div>
        <div class="absolute bottom-1/4 right-10 w-72 h-72 bg-gradient-radial from-fuchsia-600/20 to-transparent blur-3xl -z-10 animate-pulse"></div>
        <div class="relative z-10 container mx-auto px-6">
            {{-- Heading ... --}}
             <h2 class="text-4xl font-bold text-center mb-16 text-gray-100 text-shadow-md">Under the Hood: The Process</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- Process Steps ... --}}
                <div class="group"> <div class="flex flex-col items-center text-center glass-card !p-8 gradient-shadow-card"> <x-icon name="lucide-file-scan" class="h-10 w-10 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">1. Pre-processing</h3> <p class="text-gray-300 text-sm"> Files become 'Artifacts' with text, images, page shots. OCR runs if needed. </p> </div> </div>
                <div class="group"> <div class="flex flex-col items-center text-center glass-card !p-8 gradient-shadow-card"> <x-icon name="lucide-list-tree" class="h-10 w-10 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">2. Strategy & Schema</h3> <p class="text-gray-300 text-sm"> The Strategy chunks content based on your Extractor's JSON Schema. </p> </div> </div>
                <div class="group"> <div class="flex flex-col items-center text-center glass-card !p-8 gradient-shadow-card"> <x-icon name="lucide-brain-circuit" class="h-10 w-10 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">3. LLM Interaction</h3> <p class="text-gray-300 text-sm"> Chunks (text/images) are sent to the LLM using schema-guided prompts. </p> </div> </div>
                <div class="group"> <div class="flex flex-col items-center text-center glass-card !p-8 gradient-shadow-card"> <x-icon name="lucide-check-check" class="h-10 w-10 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">4. Validation & Output</h3> <p class="text-gray-300 text-sm"> LLM output is validated against the schema. Valid JSON is returned. </p> </div> </div>
            </div>
        </div>
    </div>

    {{-- Use Cases Section --}}
    <div class="container mx-auto px-6 py-24">
         {{-- Heading ... --}}
         <h2 class="text-4xl font-bold text-center mb-16 text-gray-100 text-shadow-md">Common Use Cases</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
             {{-- Use Case Cards ... --}}
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-file-input" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Automate Data Entry</h3> <p class="text-gray-300 text-sm">Extract from invoices, receipts, forms into ERP/accounting systems.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-upload" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">SaaS Smart Import</h3> <p class="text-gray-300 text-sm">Let users upload documents to populate data in your CRM or SaaS app.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-database" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Document Conversion</h3> <p class="text-gray-300 text-sm">Turn document batches (PDFs, scans) into structured JSON/CSV.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-gem" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Core Extraction Engine</h3> <p class="text-gray-300 text-sm">Power your document processing platform with Data Wizard's API.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-bar-chart-3" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Market Research</h3> <p class="text-gray-300 text-sm">Gather product/pricing data from competitor brochures or websites.</p> </div> </div>
            <div class="group"> <div class="glass-card !p-6 gradient-shadow-card"> <x-icon name="lucide-clipboard-check" class="h-8 w-8 text-cyan-400 mb-4"/> <h3 class="text-lg font-semibold mb-2 text-white">Compliance Checks</h3> <p class="text-gray-300 text-sm">Extract specific clauses or data points from contracts or reports.</p> </div> </div>
        </div>
    </div>


    {{-- CTA Section --}}
    <div id="get-started-cta" class="container mx-auto px-6 pt-12 pb-24">
        <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-white/10 group">
           {{-- Backgrounds & Shadow Wrapper ... --}}
            <div class="absolute inset-0 bg-gradient-to-br from-blue-800 via-purple-800 to-fuchsia-900 opacity-80"></div>
            <div class="absolute -top-20 -left-20 w-96 h-96 bg-gradient-radial from-cyan-500/30 to-transparent blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-gradient-radial from-fuchsia-500/30 to-transparent blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
             <div class="gradient-shadow-card">
                <div class="relative px-8 py-16 text-center z-10">
                    {{-- CTA Content ... --}}
                    <h2 class="text-4xl font-bold mb-6 text-white text-shadow-md">Ready to Automate Data Extraction?</h2>
                    <p class="text-xl text-gray-200 mb-8 max-w-2xl mx-auto text-shadow">
                        Deploy Data Wizard with Docker and start transforming documents in minutes.
                    </p>
                    <a href="/docs/quick-start" class="inline-block px-8 py-3 bg-white text-purple-900 rounded-lg font-semibold hover:bg-gray-200 transition-colors shadow-lg transform hover:scale-105">
                        Quick Start Guide
                    </a>
                     <a href="https://github.com/mateffy/data-wizard" target="_blank" rel="noopener noreferrer" class="ml-4 inline-block px-8 py-3 bg-white/10 backdrop-blur-sm border border-white/20 text-white rounded-lg font-semibold hover:bg-white/20 transition-colors shadow-lg transform hover:scale-105">
                        View on GitHub
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="container mx-auto px-6 py-12 border-t border-white/10 mt-16">
       {{-- Footer content ... --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center space-x-2">
                <x-icon name="lucide-wand" class="h-6 w-6 text-cyan-400"/>
                <span class="text-xl font-bold text-gray-200">Data Wizard</span>
            </div>
            <div class="flex items-center space-x-6 text-sm text-gray-400">
                <a href="/privacy" class="hover:text-cyan-300 transition-colors">Privacy Policy</a>
                <a href="/terms" class="hover:text-cyan-300 transition-colors">Terms of Service</a>
                <a href="/contact" class="hover:text-cyan-300 transition-colors">Contact</a>
            </div>
             <div class="text-sm text-gray-500 mt-4 md:mt-0">
                © {{ date('Y') }} Data Wizard. MIT License.
             </div>
        </div>
    </footer>

</div>
