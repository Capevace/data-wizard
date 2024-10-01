<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supermarket Brochure Analysis Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8" x-data="{
        formStep: 4,
        competitorName: '',
        brochureTitle: '',
        startDate: '',
        endDate: '',
        region: '',
        distribution: '',
        coverTheme: '',
        pageCount: '',
        specialEvents: [],
        layoutStyle: '',
        primaryColors: [],
        products: [],
        mostExpensiveProduct: null,
    }">
        <h1 class="text-3xl font-bold mb-6 text-center text-green-600">Supermarket Brochure Analysis Form</h1>

        <!-- Progress bar -->
        <div class="mb-8">
            <div class="flex justify-between mb-2">
                <span x-text="'Step ' + formStep + ' of 4'" class="text-sm font-medium text-gray-600"></span>
                <span x-text="Math.round((formStep / 4) * 100) + '%'" class="text-sm font-medium text-green-600"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-green-600 h-2.5 rounded-full" :style="'width: ' + (formStep / 4 * 100) + '%'"></div>
            </div>
        </div>

        <!-- Step 1: Basic Brochure Information -->
        <div x-show="formStep === 1">
            <h2 class="text-2xl font-semibold mb-4">Basic Brochure Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="competitorName" class="block mb-2 text-sm font-medium text-gray-700">Competitor Company Name</label>
                    <input type="text" id="competitorName" x-model="competitorName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label for="brochureTitle" class="block mb-2 text-sm font-medium text-gray-700">Brochure Title</label>
                    <input type="text" id="brochureTitle" x-model="brochureTitle" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label for="startDate" class="block mb-2 text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" id="startDate" x-model="startDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label for="endDate" class="block mb-2 text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" id="endDate" x-model="endDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
        </div>

        <!-- Step 2: Distribution and Coverage -->
        <div x-show="formStep === 2">
            <h2 class="text-2xl font-semibold mb-4">Distribution and Coverage</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="region" class="block mb-2 text-sm font-medium text-gray-700">Region</label>
                    <input type="text" id="region" x-model="region" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label for="distribution" class="block mb-2 text-sm font-medium text-gray-700">Distribution Method</label>
                    <select id="distribution" x-model="distribution" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Select Distribution Method</option>
                        <option value="direct_mail">Direct Mail</option>
                        <option value="newspaper_insert">Newspaper Insert</option>
                        <option value="in_store">In-Store</option>
                        <option value="online">Online</option>
                        <option value="app">Mobile App</option>
                    </select>
                </div>
                <div>
                    <label for="pageCount" class="block mb-2 text-sm font-medium text-gray-700">Number of Pages</label>
                    <input type="number" id="pageCount" x-model="pageCount" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
        </div>

        <!-- Step 3: Design and Layout -->
        <div x-show="formStep === 3">
            <h2 class="text-2xl font-semibold mb-4">Design and Layout</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="coverTheme" class="block mb-2 text-sm font-medium text-gray-700">Cover Theme</label>
                    <input type="text" id="coverTheme" x-model="coverTheme" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label for="layoutStyle" class="block mb-2 text-sm font-medium text-gray-700">Layout Style</label>
                    <select id="layoutStyle" x-model="layoutStyle" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Select Layout Style</option>
                        <option value="grid">Grid</option>
                        <option value="list">List</option>
                        <option value="mixed">Mixed</option>
                        <option value="category_based">Category-based</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Primary Colors Used</label>
                    <input type="text" x-model="primaryColors" @input="primaryColors = $event.target.value.split(',')" placeholder="Enter colors, separated by commas" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    <div class="mt-2 flex flex-wrap gap-2">
                        <template x-for="color in primaryColors" :key="color">
                            <span class="bg-gray-200 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded" x-text="color.trim()"></span>
                        </template>
                    </div>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Special Events/Themes</label>
                    <template x-for="(event, index) in specialEvents" :key="index">
                        <div class="flex items-center mb-2">
                            <input type="text" x-model="specialEvents[index]" class="flex-grow px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <button @click="specialEvents.splice(index, 1)" class="ml-2 px-2 py-1 bg-red-500 text-white rounded-md">Remove</button>
                        </div>
                    </template>
                    <button @click="specialEvents.push('')" class="px-4 py-2 bg-green-500 text-white rounded-md">Add Event/Theme</button>
                </div>
            </div>
        </div>

        <!-- Step 4: Product Table -->
        <div
            x-show="formStep === 4"
            x-data="{
                init() {
                    const url = Magic.iframe.url({
                        extractorId: 'c63eefc4-1041-4cf7-a8bf-2f63784fb95f',
{{--                            bucketId: 'ccdfbdd4-2489-457b-8ce5-d73748a6ce11',--}}
{{--                            runId: 'ccdfbdd4-2489-457b-8ce5-d73748a6ce11',--}}
{{--                            signature: 'ccdfbdd4-2489-457b-8ce5-d73748a6ce11',--}}
                    });

                    const iframe = Magic.iframe.create(
{{--                        'https://magic-import.test/embed/c63eefc4-1041-4cf7-a8bf-2f63784fb95f?bucketId=7167b5d4-813c-494b-a29e-5f8384a9c2f2&runId=e66016ea-417f-4a18-83b8-88eedf91d9ed&signature=d29f4b206660fdae6845ed02b5fa1283232158d8a54cc2d50a76fa079d24cd42&bucket=7167b5d4-813c-494b-a29e-5f8384a9c2f2',--}}
{{--                        'https://magic.mateffy.me/embed/a8a96ff1-ca3d-471e-bad7-7c6aaf587a52?bucket=50888dfd-6267-46d5-aaa0-ca8cc98c55b7&signature=daefa8b12cd27f44d16c6863aa691e52cfca76582a0b759b95f9ea47b7522db8',--}}
                            'https://magic.mateffy.me/embed/a4dbbf5a-4d78-444f-8c6e-9b583d8f53ff?bucket=0d20d27f-da14-4fd2-92b2-710bae3c5d6a&signature=79b12b2ab5b2d67eb4febac835b29296d6aead12cf71905232e69cdfb7a83609&step=bucket',
                        { container: this.$refs.iframeContainer, keepBorder: true }
                    );

                    iframe.className = 'w-full h-full overflow-y-auto rounded-lg border border-gray-300 shadow-inner';

                    Magic.iframe.onSubmit((data) => {
                        this.products = data.products;
                        this.mostExpensiveProduct = data.products.sort((a, b) => b.price - a.price)[0] ?? null;
                    });
                }
            }"
        >
            <h2 class="text-2xl font-semibold mb-4">Product Information</h2>
            <p class="mb-4">Use the form below to enter detailed product information from the brochure.</p>
            <script src="{{ asset('js/magic-iframe.js') }}"></script>
            <div class="mt-4 " x-ref="iframeContainer" x-show="products.length === 0"></div>

{{--            <script src="https://magic-import.test/livewire/livewire.js?id=cc800bf4"></script>--}}
{{--            <livewire data-component="embedded-extractor" data-params='{"extractorId": "c63eefc4-1041-4cf7-a8bf-2f63784fb95f" }'></livewire>--}}

            <div x-show="products.length > 0" class="prose prose-sm">
                <p>You have identified <span x-text="products.length"></span> products.</p>
                <p>The most expensive product is <span x-text="mostExpensiveProduct.name"></span> for <span x-text="mostExpensiveProduct.price"></span> €.</p>
            </div>
        </div>

        <!-- Navigation buttons -->
        <div class="mt-8 flex justify-between">
            <button
                x-show="formStep > 1"
                @click="formStep--"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
                Previous
            </button>
            <button
                x-show="formStep < 4"
                @click="formStep++"
                class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500"
            >
                Next
            </button>
            <button
                x-show="formStep === 4 && products.length > 0"
                @click="alert('Form submitted!')"
                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                Submit Analysis
            </button>
        </div>
    </div>
</body>
</html>
