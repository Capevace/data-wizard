<div id="features-interactive" class="py-24 relative overflow-hidden">
    {{-- Background overlay & subtle glow --}}
    <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/40 to-black/50 backdrop-blur-md"></div>
    <div class="absolute top-1/4 left-10 w-[20rem] h-[20rem] md:w-72 md:h-72 bg-gradient-radial from-blue-600/25 to-transparent blur-3xl -z-10 animate-pulse"></div>
    <div class="absolute bottom-1/4 right-10 w-[20rem] h-[20rem] md:w-72 md:h-72 bg-gradient-radial from-purple-600/15 to-transparent blur-3xl -z-10 animate-pulse" style="animation-delay: 1.5s;"></div>

    <div class="relative z-10 container mx-auto px-6">
        <h2 class="text-4xl font-bold text-center mb-16 text-gray-100 text-shadow-md">Key Features</h2>

        <div
            x-data="{
                activeFeature: 0,
                featureCount: 6,
                intervalDuration: 7000, // ms
                timerInstance: null,
                isHovering: false,
                progressKey: Date.now(), // Initialize with a unique value

                // --- Methods ---
                nextFeature() {
                    this.activeFeature = (this.activeFeature + 1) % this.featureCount;
                    this.resetProgressAnimation(); // Reset animation when feature changes
                },
                selectFeature(index) {
                    if (this.activeFeature === index) return; // Do nothing if already active
                    this.activeFeature = index;
                    this.stopTimer(); // Stop existing timer
                    this.resetProgressAnimation(); // Reset animation for the new feature
                    this.startTimer(true); // Restart timer immediately after interaction
                },
                startTimer(immediateStart = false) {
                    if (this.timerInstance) clearInterval(this.timerInstance);

                    // Function to execute the next feature step
                    const step = () => {
                         if (!this.isHovering) {
                            this.nextFeature();
                        }
                    };

                    // If immediateStart is false (like on init), wait one interval before the first step
                    // Otherwise (like after a click), start the interval immediately for subsequent steps
                    if (!immediateStart) {
                         this.timerInstance = setTimeout(() => {
                            step(); // First step after initial delay
                            // Now set the regular interval
                            this.timerInstance = setInterval(step, this.intervalDuration);
                        }, this.intervalDuration);
                    } else {
                         // Set the regular interval immediately
                         this.timerInstance = setInterval(step, this.intervalDuration);
                    }

                    // Ensure the progress bar animation starts visually right away
                    this.resetProgressAnimation();
                },
                stopTimer() {
                    if (this.timerInstance) {
                        clearInterval(this.timerInstance);
                        clearTimeout(this.timerInstance); // Clear timeout too, just in case
                        this.timerInstance = null;
                    }
                },
                pauseTimer() { // Called on mouseenter
                    this.isHovering = true;
                    // CSS class will handle pausing the visual animation
                },
                resumeTimer() { // Called on mouseleave
                    this.isHovering = false;
                    // CSS class will handle resuming the visual animation
                },
                resetProgressAnimation() {
                     // Changing the key forces Alpine to re-render the element, restarting the CSS animation
                    this.progressKey = Date.now();
                },

                // --- Lifecycle ---
                init() {
                    this.startTimer(false); // Start timer on load, don't trigger first step immediately
                    // Add listener to restart timer if window loses/gains focus (optional but good)
                    window.addEventListener('focus', () => this.startTimer(true));
                    window.addEventListener('blur', () => this.stopTimer());
                },
                destroy() {
                     this.stopTimer(); // Cleanup on component removal
                     window.removeEventListener('focus', () => this.startTimer(true));
                     window.removeEventListener('blur', () => this.stopTimer());
                }
            }"
            x-init="init()"
            x-on:destroy="destroy()"
            @mouseenter="pauseTimer()"
            @mouseleave="resumeTimer()"
            class="grid md:grid-cols-3 gap-8 lg:gap-12 items-start"
        >
            {{-- Left Column: Feature Titles --}}
            <div class="md:col-span-1 space-y-3 sticky top-24">
                {{-- Loop through features for cleaner code (Requires passing features from backend or defining in x-data) --}}
                {{-- Example for one button (repeat or loop for others) --}}
                <x-landing.key-features.button @click="selectFeature(0)" active-feature="0">
                    <span class="font-semibold block">JSON-based Extraction Engine</span>
                    {{-- Progress Bar Container (Always present for structure) --}}
                    <div class="h-1 mt-2.5 overflow-hidden rounded bg-white/10">
                         {{-- The actual animating bar --}}
                        <div x-show="activeFeature === 0"
                             :key="'progress-' + activeFeature + '-' + progressKey" {{-- Key includes feature index and reset key --}}
                             class="h-full bg-gradient-to-r from-cyan-400 to-purple-500 rounded animate-progress"
                             :class="{ 'animation-paused': isHovering }" {{-- Pause animation via class --}}
                             :style="{ animationDuration: intervalDuration + 'ms' }">
                        </div>
                    </div>
                </x-landing.key-features.button>

                 {{-- Repeat for Feature 2 --}}
                <x-landing.key-features.button @click="selectFeature(1)" active-feature="1">
                    <span class="font-semibold block">Seamless Integration via iFrame and API</span>
                    <div class="h-1 mt-2.5 overflow-hidden rounded bg-white/10">
                        <div x-show="activeFeature === 1" :key="'progress-' + activeFeature + '-' + progressKey" class="h-full bg-gradient-to-r from-cyan-400 to-purple-500 rounded animate-progress" :class="{ 'animation-paused': isHovering }" :style="{ animationDuration: intervalDuration + 'ms' }"></div>
                    </div>
                </x-landing.key-features.button>

                {{-- Repeat for Feature 3 --}}
                <x-landing.key-features.button @click="selectFeature(2)" active-feature="2">
                    <span class="font-semibold block">Handle PDF, DOCX, JPEG and more</span>
                    <div class="h-1 mt-2.5 overflow-hidden rounded bg-white/10">
                        <div x-show="activeFeature === 2" :key="'progress-' + activeFeature + '-' + progressKey" class="h-full bg-gradient-to-r from-cyan-400 to-purple-500 rounded animate-progress" :class="{ 'animation-paused': isHovering }" :style="{ animationDuration: intervalDuration + 'ms' }"></div>
                    </div>
                </x-landing.key-features.button>

                 {{-- Repeat for Feature 4 --}}
                <x-landing.key-features.button @click="selectFeature(3)" active-feature="3">
                    <span class="font-semibold block">Work with Embedded Images</span>
                    <div class="h-1 mt-2.5 overflow-hidden rounded bg-white/10">
                        <div x-show="activeFeature === 3" :key="'progress-' + activeFeature + '-' + progressKey" class="h-full bg-gradient-to-r from-cyan-400 to-purple-500 rounded animate-progress" :class="{ 'animation-paused': isHovering }" :style="{ animationDuration: intervalDuration + 'ms' }"></div>
                    </div>
                </x-landing.key-features.button>

                 {{-- Repeat for Feature 5 --}}
                <x-landing.key-features.button @click="selectFeature(4)" active-feature="4">
                    <span class="font-semibold block">Open Source & Extensible</span>
                    <div class="h-1 mt-2.5 overflow-hidden rounded bg-white/10">
                        <div x-show="activeFeature === 4" :key="'progress-' + activeFeature + '-' + progressKey" class="h-full bg-gradient-to-r from-cyan-400 to-purple-500 rounded animate-progress" :class="{ 'animation-paused': isHovering }" :style="{ animationDuration: intervalDuration + 'ms' }"></div>
                    </div>
                </x-landing.key-features.button>

                 {{-- Repeat for Feature 6 --}}
                <x-landing.key-features.button @click="selectFeature(5)" active-feature="5">
                    <span class="font-semibold block">Validate your Data using JSON Schema</span>
                    <div class="h-1 mt-2.5 overflow-hidden rounded bg-white/10">
                        <div x-show="activeFeature === 5" :key="'progress-' + activeFeature + '-' + progressKey" class="h-full bg-gradient-to-r from-cyan-400 to-purple-500 rounded animate-progress" :class="{ 'animation-paused': isHovering }" :style="{ animationDuration: intervalDuration + 'ms' }"></div>
                    </div>
                </x-landing.key-features.button>
                {{-- End Repeat --}}

            </div>

            {{-- Right Column: Feature Details (Dynamic Content) --}}
            <div class="md:col-span-2 min-h-[350px] md:min-h-[420px] relative"> {{-- Increased min-height slightly --}}
                {{-- Feature Detail Structure (Repeat for each feature index 0-5) --}}
                <x-landing.key-features.content x-show="activeFeature === 0" >
                    {{-- Feature 1 Content --}}
                    <x-icon name="lucide-puzzle" class="h-10 w-10 text-cyan-400 mb-4"/>
                    <h3 class="text-2xl font-semibold mb-3 text-white">Flexible Extraction Engine</h3>
                    <p class="text-gray-300 leading-relaxed">Define your exact data needs using standard JSON Schema. Data Wizard adapts its extraction process based on your schema, chosen LLM (like OpenAI, Anthropic, Mistral, or local models via Ollama/LMStudio), and selected processing strategy to fit the complexity of any document.</p>
                </x-landing.key-features.content>

                <x-landing.key-features.content x-show="activeFeature === 1" >
                    <x-icon name="lucide-code" class="h-10 w-10 text-cyan-400 mb-4"/>
                    <h3 class="text-2xl font-semibold mb-3 text-white">Seamless Integration</h3>
                    <p class="text-gray-300 leading-relaxed">Easily incorporate Data Wizard into your existing workflows. Embed the user-friendly upload component via an iFrame, interact programmatically using comprehensive RESTful or GraphQL APIs, or set up webhooks to receive real-time notifications with the extracted JSON data upon completion.</p>
                </x-landing.key-features.content>

                 <x-landing.key-features.content x-show="activeFeature === 2" >
                    <x-icon name="lucide-file-check-2" class="h-10 w-10 text-cyan-400 mb-4"/>
                    <h3 class="text-2xl font-semibold mb-3 text-white">Robust Document Handling</h3>
                    <p class="text-gray-300 leading-relaxed">Process a wide array of file types including PDFs (native and scanned), Word documents (DOCX), Excel spreadsheets (XLSX), images (PNG, JPG), and more. Built-in OCR automatically handles scanned documents, while image extraction and page screenshot generation provide crucial visual context to the LLM for improved accuracy.</p>
                </x-landing.key-features.content>

                 <x-landing.key-features.content x-show="activeFeature === 3" >
                    <x-icon name="lucide-image" class="h-10 w-10 text-cyan-400 mb-4"/>
                    <h3 class="text-2xl font-semibold mb-3 text-white">Reference Embedded Images</h3>
                    <p class="text-gray-300 leading-relaxed">Go beyond text extraction. Data Wizard can identify, extract, and associate images embedded within your documents (like product photos in a PDF catalog or diagrams in a report) with corresponding data points in the final JSON output, providing richer, more complete structured information.</p>
                </x-landing.key-features.content>

                 <x-landing.key-features.content x-show="activeFeature === 4" >
                    <x-icon name="lucide-github" class="h-10 w-10 text-cyan-400 mb-4"/>
                    <h3 class="text-2xl font-semibold mb-3 text-white">Open Source & Extensible</h3>
                    <p class="text-gray-300 leading-relaxed">Built with transparency and flexibility in mind. Deploy easily using Docker for self-hosting and complete data control. The MIT license allows permissive use, and the modular architecture enables developers to create and integrate custom extraction strategies or pre/post-processing steps for highly specialized workflows.</p>
                </x-landing.key-features.content>

                 <x-landing.key-features.content x-show="activeFeature === 5" >
                    {{-- Feature 6 Content --}}
                    {{-- Use a different icon for this feature --}}
                    <x-icon name="lucide-shield-check" class="h-10 w-10 text-cyan-400 mb-4"/>
                    <h3 class="text-2xl font-semibold mb-3 text-white">Validation & Reliability</h3>
                    <p class="text-gray-300 leading-relaxed">Ensure data quality and consistency. After the LLM extracts information, Data Wizard rigorously validates the output against the provided JSON Schema. Only data that perfectly matches the defined structure, types, and constraints is returned, guaranteeing clean, reliable, and immediately usable JSON.</p>
                </x-landing.key-features.content>
                {{-- End Feature Details --}}
            </div>
        </div>
    </div>
