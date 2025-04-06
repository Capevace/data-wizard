{{-- Main Background Gradient --}}
<div class="min-h-screen bg-gradient-to-b from-[#5A94B1] via-[#3A6C83] to-[#1A2F3B] text-white font-sans">

    {{-- Hero Section --}}
    <div class="relative overflow-hidden pb-16 pt-6">
        {{-- Animated Background Elements (Subtle) --}}
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_50%,rgba(122,184,217,0.1),transparent_60%)] animate-pulse"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,rgba(122,184,217,0.08),transparent_60%)] animate-pulse" style="animation-delay: 1s;"></div>

        {{-- Navigation --}}
        <nav class="relative z-10 container mx-auto px-6 py-6">
            <div class="flex items-center justify-between">
                <a href="/" class="flex items-center space-x-2 group">
                    <x-icon name="lucide-wand" class="h-8 w-8 text-[#7AB8D9] group-hover:text-white transition-colors duration-300"/>
                    <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-[#A0D2EB] to-[#E0F2F7]">
                        Data Wizard
                    </span>
                </a>
                <div class="flex items-center space-x-6">
                    <a href="https://github.com/mateffy/data-wizard" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-2 text-gray-200 hover:text-white transition-colors">
                        <x-icon name="lucide-github" class="h-5 w-5"/>
                        <span>GitHub</span>
                    </a>
                    <a href="/docs" class="flex items-center space-x-2 text-gray-200 hover:text-white transition-colors">
                        <x-icon name="lucide-file-text" class="h-5 w-5"/>
                        <span>Docs</span>
                    </a>
                </div>
            </div>
        </nav>

        {{-- Hero Content --}}
        <div class="container mx-auto px-6 pt-16 pb-24 text-center">
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-center mb-6">
                    <x-icon name="lucide-sparkles" class="h-12 w-12 text-[#A0D2EB] animate-pulse"/>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-[#E0F2F7] via-[#A0D2EB] to-[#E0F2F7]">
                    Extract Structured Data from Any Document
                </h1>
                <p class="text-xl md:text-2xl text-gray-200 mb-10">
                    Turn documents like PDFs, Word files, and images into structured, validated JSON using AI. Open-source and easy to integrate.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="#get-started" class="px-8 py-3 bg-[#7AB8D9] text-[#1A2F3B] rounded-lg font-semibold hover:bg-[#A0D2EB] transition-colors shadow-lg">
                        Get Started
                    </a>
                    <a href="/docs" class="px-8 py-3 bg-white/10 rounded-lg font-semibold backdrop-blur-sm hover:bg-white/20 transition-colors border border-white/20">
                        View Documentation
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- What is Data Wizard Section --}}
    <div class="py-24 bg-[#1A2F3B]/20">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-bold text-center mb-12 text-gray-100">What is Data Wizard?</h2>
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <p class="text-lg text-gray-300 mb-6">
                        Data Wizard is an open-source tool that uses Large Language Models (LLMs) to pull structured data from various documents.
                    </p>
                    <p class="text-lg text-gray-300 mb-6">
                        Simply define your desired output using <strong>JSON Schema</strong>, pick an <strong>Extraction Strategy</strong>, choose your <strong>LLM</strong> (like OpenAI, Anthropic, or local models), and let Data Wizard extract and validate the data.
                    </p>
                    <p class="text-lg text-gray-300">
                        Ideal for automating data entry or adding smart import features to your apps. Integrate via <strong>iFrame</strong> or programmatically with the <strong>REST/GraphQL API</strong>.
                    </p>
                </div>
                <div class="text-center p-4 rounded-lg bg-gradient-to-br from-white/5 to-transparent border border-white/10">
                    <x-diagrams.overview />
                    <p class="text-sm text-gray-400 mt-2">Core Extraction Flow</p>
                </div>
            </div>
        </div>
    </div>

    {{-- How to Use Section --}}
    <div id="how-to-use" class="container mx-auto px-6 py-24">
        <h2 class="text-4xl font-bold text-center mb-16 text-gray-100">Getting Started</h2>
        <div class="grid md:grid-cols-3 gap-8">
            {{-- Step 1 --}}
            <div class="relative p-8 rounded-2xl bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg border border-white/15 group hover:scale-[1.03] transition-transform duration-300 shadow-lg">
                <div class="absolute -top-4 -left-4 p-3 bg-[#7AB8D9]/20 rounded-lg backdrop-blur-sm">
                    <x-icon name="lucide-settings-2" class="h-6 w-6 text-[#A0D2EB]"/>
                </div>
                <h3 class="text-xl font-semibold mb-4 text-white">1. Configure Extractor</h3>
                <p class="text-gray-300">
                    Define the data structure you need with JSON Schema. Select an LLM, an extraction strategy, and optionally add specific instructions.
                </p>
            </div>

            {{-- Step 2 --}}
            <div class="relative p-8 rounded-2xl bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg border border-white/15 group hover:scale-[1.03] transition-transform duration-300 shadow-lg">
                <div class="absolute -top-4 -left-4 p-3 bg-[#7AB8D9]/20 rounded-lg backdrop-blur-sm">
                     <x-icon name="lucide-file-up" class="h-6 w-6 text-[#A0D2EB]"/>
                </div>
                <h3 class="text-xl font-semibold mb-4 text-white">2. Upload Documents</h3>
                <p class="text-gray-300">
                    Upload your files (PDFs, images, etc.) through the UI, the embeddable iFrame, or directly via the REST API.
                </p>
            </div>

            {{-- Step 3 --}}
            <div class="relative p-8 rounded-2xl bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg border border-white/15 group hover:scale-[1.03] transition-transform duration-300 shadow-lg">
                <div class="absolute -top-4 -left-4 p-3 bg-[#7AB8D9]/20 rounded-lg backdrop-blur-sm">
                    <x-icon name="lucide-check-check" class="h-6 w-6 text-[#A0D2EB]"/>
                </div>
                <h3 class="text-xl font-semibold mb-4 text-white">3. Get Structured Data</h3>
                <p class="text-gray-300">
                    The AI extracts and validates data against your schema. Receive clean JSON via the UI, download, webhook, or API call.
                </p>
            </div>
        </div>
    </div>

     {{-- Key Features Section --}}
    <div id="features" class="py-24 bg-[#1A2F3B]/20">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-bold text-center mb-16 text-gray-100">Key Features</h2>
            <div class="grid md:grid-cols-2 gap-12 items-start">
                {{-- Left Column - Feature List --}}
                <div class="space-y-8">
                    {{-- Feature 1 --}}
                    <div class="p-6 rounded-xl bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg border border-white/15 hover:border-[#7AB8D9]/30 transition-colors shadow-lg flex items-start space-x-4">
                        <div class="p-3 bg-[#7AB8D9]/20 rounded-lg backdrop-blur-sm shrink-0 mt-1">
                            <x-icon name="lucide-puzzle" class="h-6 w-6 text-[#A0D2EB]"/>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2 text-white">Flexible Extraction Engine</h3>
                            <p class="text-gray-300 text-sm">Use JSON Schema for precise output. Choose LLMs (OpenAI, Anthropic, local) & strategies per task.</p>
                        </div>
                    </div>

                    {{-- Feature 2 --}}
                     <div class="p-6 rounded-xl bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg border border-white/15 hover:border-[#7AB8D9]/30 transition-colors shadow-lg flex items-start space-x-4">
                        <div class="p-3 bg-[#7AB8D9]/20 rounded-lg backdrop-blur-sm shrink-0 mt-1">
                            <x-icon name="lucide-code" class="h-6 w-6 text-[#A0D2EB]"/>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2 text-white">Seamless Integration</h3>
                            <p class="text-gray-300 text-sm">Embed via iFrame (with JS API) or use REST/GraphQL APIs. Get updates via Webhooks.</p>
                        </div>
                    </div>

                    {{-- Feature 3 --}}
                    <div class="p-6 rounded-xl bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg border border-white/15 hover:border-[#7AB8D9]/30 transition-colors shadow-lg flex items-start space-x-4">
                        <div class="p-3 bg-[#7AB8D9]/20 rounded-lg backdrop-blur-sm shrink-0 mt-1">
                            <x-icon name="lucide-github" class="h-6 w-6 text-[#A0D2EB]"/>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2 text-white">Open Source & Extensible</h3>
                            <p class="text-gray-300 text-sm">Self-host with Docker (MIT License). Build custom PHP strategies for unique workflows.</p>
                        </div>
                    </div>

                    {{-- Feature 4 --}}
                    <div class="p-6 rounded-xl bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg border border-white/15 hover:border-[#7AB8D9]/30 transition-colors shadow-lg flex items-start space-x-4">
                        <div class="p-3 bg-[#7AB8D9]/20 rounded-lg backdrop-blur-sm shrink-0 mt-1">
                            <x-icon name="lucide-file-check-2" class="h-6 w-6 text-[#A0D2EB]"/>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-2 text-white">Robust Document Handling</h3>
                            <p class="text-gray-300 text-sm">Processes PDFs, DOCX, images, etc. Includes OCR, image extraction & page screenshots for context.</p>
                        </div>
                    </div>
                </div>

                {{-- Right Column - Code Preview --}}
                <div class="sticky top-24"> {{-- Make code preview sticky --}}
                    <div class="relative p-6 rounded-xl bg-gradient-to-br from-[#1A2F3B] to-[#2A4C5E]/80 overflow-hidden group border border-white/10 shadow-xl">
                         <h3 class="text-xl font-semibold mb-4 text-white">Example: Invoice JSON Schema</h3>
                         <div class="max-h-96 overflow-y-auto"> {{-- Limit height and enable scroll --}}
                            <pre class="text-xs text-gray-200 overflow-x-auto bg-black/30 p-4 rounded-md"><code class="language-json">{
  "title": "Invoice",
  "description": "Schema for basic invoice data",
  "type": "object",
  "properties": {
    "invoiceNumber": {
      "type": "string",
      "description": "Unique invoice identifier"
    },
    "issueDate": { "type": "string", "format": "date" },
    "currency": { "type": "string", "enum": ["EUR", "USD"] },
    "seller": {
      "type": "object",
      "properties": { /* ... seller details ... */ },
      "required": ["name"]
    },
    "buyer": { /* ... buyer details ... */ },
    "lineItems": {
      "type": "array",
      "description": "List of invoice items",
      "magic_ui": "table", // UI Hint
      "items": { /* ... item details ... */ }
    },
    "totalAmounts": { /* ... totals ... */ }
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
        <h2 class="text-4xl font-bold text-center mb-16 text-gray-100">Flexible Extraction Strategies</h2>
        <p class="text-center text-lg text-gray-300 mb-12 max-w-3xl mx-auto">
            Choose the best strategy for your document type and complexity. Strategies control how documents are processed and sent to the LLM.
        </p>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="p-6 rounded-xl bg-white/5 border border-white/10 shadow-lg">
                <x-icon name="lucide-file" class="h-8 w-8 text-[#A0D2EB] mb-4"/>
                <h3 class="text-lg font-semibold mb-2 text-white">Simple</h3>
                <p class="text-gray-300 text-sm">Sends the entire document (or as much as fits) to the LLM in one go. Best for small documents.</p>
            </div>
            <div class="p-6 rounded-xl bg-white/5 border border-white/10 shadow-lg">
                <x-icon name="lucide-arrow-right-left" class="h-8 w-8 text-[#A0D2EB] mb-4"/>
                <h3 class="text-lg font-semibold mb-2 text-white">Sequential</h3>
                <p class="text-gray-300 text-sm">Processes document chunks one by one, feeding results from the previous chunk into the next prompt. Maintains context.</p>
            </div>
            <div class="p-6 rounded-xl bg-white/5 border border-white/10 shadow-lg">
                <x-icon name="lucide-git-fork" class="h-8 w-8 text-[#A0D2EB] mb-4"/>
                <h3 class="text-lg font-semibold mb-2 text-white">Parallel</h3>
                <p class="text-gray-300 text-sm">Processes document chunks independently and simultaneously. Good for documents with unrelated data points across pages.</p>
            </div>
            <div class="p-6 rounded-xl bg-white/5 border border-white/10 shadow-lg">
                 <x-icon name="lucide-merge" class="h-8 w-8 text-[#A0D2EB] mb-4"/>
                <h3 class="text-lg font-semibold mb-2 text-white">Auto-Merging</h3>
                <p class="text-gray-300 text-sm">Extends Sequential or Parallel. Concatenates results from chunks and runs a final LLM call to deduplicate. Helps prevent lost items.</p>
            </div>
             <div class="p-6 rounded-xl bg-white/5 border border-white/10 shadow-lg">
                <x-icon name="lucide-repeat-2" class="h-8 w-8 text-[#A0D2EB] mb-4"/>
                <h3 class="text-lg font-semibold mb-2 text-white">Double-Pass</h3>
                <p class="text-gray-300 text-sm">First pass uses Parallel, second pass uses Sequential to review and refine. Combines speed and accuracy. Supports auto-merging.</p>
            </div>
             <div class="p-6 rounded-xl bg-white/5 border border-white/10 shadow-lg">
                <x-icon name="lucide-flask-conical" class="h-8 w-8 text-[#A0D2EB] mb-4"/>
                <h3 class="text-lg font-semibold mb-2 text-white">Custom Strategy</h3>
                <p class="text-gray-300 text-sm">Build your own strategy in PHP by implementing an interface. Add custom logic, validation, or API calls.</p>
            </div>
        </div>
    </div>


    {{-- How It Works (Technical Details) Section --}}
    <div id="how-it-works-tech" class="py-24 bg-[#1A2F3B]/20">
        <div class="container mx-auto px-6">
             <h2 class="text-4xl font-bold text-center mb-16 text-gray-100">Under the Hood: The Process</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- Step 1: Upload & Preprocessing --}}
                <div class="flex flex-col items-center text-center p-6 bg-white/5 rounded-xl border border-white/10 shadow-lg">
                     <x-icon name="lucide-file-scan" class="h-10 w-10 text-[#A0D2EB] mb-4"/>
                    <h3 class="text-lg font-semibold mb-2 text-white">1. Pre-processing</h3>
                    <p class="text-gray-300 text-sm">
                        Files become 'Artifacts' with text, images, page shots. OCR runs if needed.
                    </p>
                </div>
                {{-- Step 2: Strategy Application --}}
                <div class="flex flex-col items-center text-center p-6 bg-white/5 rounded-xl border border-white/10 shadow-lg">
                     <x-icon name="lucide-list-tree" class="h-10 w-10 text-[#A0D2EB] mb-4"/>
                    <h3 class="text-lg font-semibold mb-2 text-white">2. Strategy & Schema</h3>
                    <p class="text-gray-300 text-sm">
                        The Strategy chunks content based on your Extractor's JSON Schema.
                    </p>
                </div>
                {{-- Step 3: LLM Interaction --}}
                <div class="flex flex-col items-center text-center p-6 bg-white/5 rounded-xl border border-white/10 shadow-lg">
                    <x-icon name="lucide-brain-circuit" class="h-10 w-10 text-[#A0D2EB] mb-4"/>
                    <h3 class="text-lg font-semibold mb-2 text-white">3. LLM Interaction</h3>
                    <p class="text-gray-300 text-sm">
                        Chunks (text/images) are sent to the LLM using schema-guided prompts.
                    </p>
                </div>
                 {{-- Step 4: Validation & Output --}}
                <div class="flex flex-col items-center text-center p-6 bg-white/5 rounded-xl border border-white/10 shadow-lg">
                    <x-icon name="lucide-check-check" class="h-10 w-10 text-[#A0D2EB] mb-4"/>
                    <h3 class="text-lg font-semibold mb-2 text-white">4. Validation & Output</h3>
                    <p class="text-gray-300 text-sm">
                        LLM output is validated against the schema. Valid JSON is returned.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Use Cases Section --}}
    <div class="container mx-auto px-6 py-24">
         <h2 class="text-4xl font-bold text-center mb-16 text-gray-100">Common Use Cases</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="p-6 rounded-xl bg-white/5 border border-white/10 shadow-lg">
                <x-icon name="lucide-file-input" class="h-8 w-8 text-[#A0D2EB] mb-4"/>
                <h3 class="text-lg font-semibold mb-2 text-white">Automate Data Entry</h3>
                <p class="text-gray-300 text-sm">Extract from invoices, receipts, forms into ERP/accounting systems.</p>
            </div>
             <div class="p-6 rounded-xl bg-white/5 border border-white/10 shadow-lg">
                <x-icon name="lucide-upload" class="h-8 w-8 text-[#A0D2EB] mb-4"/>
                <h3 class="text-lg font-semibold mb-2 text-white">SaaS Smart Import</h3>
                <p class="text-gray-300 text-sm">Let users upload documents to populate data in your CRM or SaaS app.</p>
            </div>
             <div class="p-6 rounded-xl bg-white/5 border border-white/10 shadow-lg">
                <x-icon name="lucide-database" class="h-8 w-8 text-[#A0D2EB] mb-4"/>
                <h3 class="text-lg font-semibold mb-2 text-white">Document Conversion</h3>
                <p class="text-gray-300 text-sm">Turn document batches (PDFs, scans) into structured JSON/CSV.</p>
            </div>
            <div class="p-6 rounded-xl bg-white/5 border border-white/10 shadow-lg">
                <x-icon name="lucide-gem" class="h-8 w-8 text-[#A0D2EB] mb-4"/>
                <h3 class="text-lg font-semibold mb-2 text-white">Core Extraction Engine</h3>
                <p class="text-gray-300 text-sm">Power your document processing platform with Data Wizard's API.</p>
            </div>
            <div class="p-6 rounded-xl bg-white/5 border border-white/10 shadow-lg">
                <x-icon name="lucide-bar-chart-3" class="h-8 w-8 text-[#A0D2EB] mb-4"/>
                <h3 class="text-lg font-semibold mb-2 text-white">Market Research</h3>
                <p class="text-gray-300 text-sm">Gather product/pricing data from competitor brochures or websites.</p>
            </div>
             <div class="p-6 rounded-xl bg-white/5 border border-white/10 shadow-lg">
                <x-icon name="lucide-clipboard-check" class="h-8 w-8 text-[#A0D2EB] mb-4"/>
                <h3 class="text-lg font-semibold mb-2 text-white">Compliance Checks</h3>
                <p class="text-gray-300 text-sm">Extract specific clauses or data points from contracts or reports.</p>
            </div>
        </div>
    </div>


    {{-- CTA Section --}}
    <div id="get-started" class="container mx-auto px-6 py-24">
        <div class="relative rounded-3xl overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-r from-[#6aa9c7] via-[#47819E] to-[#3A6C83] opacity-95"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_40%,rgba(255,255,255,0.1),transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_70%,rgba(255,255,255,0.08),transparent_50%)] animate-pulse" style="animation-delay: 0.5s;"></div>
            <div class="relative px-8 py-16 text-center">
                <h2 class="text-4xl font-bold mb-6 text-white text-shadow-md">Ready to Automate Data Extraction?</h2>
                <p class="text-xl text-gray-100 mb-8 max-w-2xl mx-auto text-shadow">
                    Deploy Data Wizard with Docker and start transforming documents in minutes.
                </p>
                <a href="/docs/quick-start" class="inline-block px-8 py-3 bg-white text-[#3A6C83] rounded-lg font-semibold hover:bg-gray-200 transition-colors shadow-lg transform hover:scale-105">
                    Quick Start Guide
                </a>
                 <a href="https://github.com/mateffy/data-wizard" target="_blank" rel="noopener noreferrer" class="ml-4 inline-block px-8 py-3 bg-transparent border border-white text-white rounded-lg font-semibold hover:bg-white/10 transition-colors shadow-lg transform hover:scale-105">
                    View on GitHub
                </a>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="container mx-auto px-6 py-12 border-t border-white/10 mt-16">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center space-x-2">
                <x-icon name="lucide-wand" class="h-6 w-6 text-[#7AB8D9]"/>
                <span class="text-xl font-bold text-gray-200">Data Wizard</span>
            </div>
            <div class="flex items-center space-x-6 text-sm text-gray-400">
                <a href="/privacy" class="hover:text-[#A0D2EB] transition-colors">Privacy Policy</a>
                <a href="/terms" class="hover:text-[#A0D2EB] transition-colors">Terms of Service</a>
                <a href="/contact" class="hover:text-[#A0D2EB] transition-colors">Contact</a>
            </div>
             <div class="text-sm text-gray-500 mt-4 md:mt-0">
                © {{ date('Y') }} Data Wizard. MIT License.
             </div>
        </div>
    </footer>
</div>
