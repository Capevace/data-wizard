<div class="py-24 relative gradient-shadow-always-on overflow-hidden shadow-inner">
      <div class="absolute inset-0 bg-gradient-to-b from-black/10 to-black/30 backdrop-blur-sm"></div>
        <div class="relative z-10 container mx-auto px-6">
            <div class="grid xl:grid-cols-2 gap-16 items-start"> {{-- items-stretch might be needed if content differs vastly --}}
                <div class="text-lg text-gray-300 flex flex-col gap-6">
                    <h2 class="text-4xl font-bold text-gray-100 text-shadow-md flex gap-8 items-center">
                        <span>Get started with Docker</span>
                        <x-icon name="fab-docker" class="inline-block h-14 w-14 text-gray-100"/>
                    </h2>
                    <p>
                        Using Data Wizard is as simple as running a single Docker command. In just a few minutes, you can have a fully functional instance of Data Wizard up and running, ready to process your documents – self-hosted and on your own infrastructure.
                    </p>
                    <p>
                        Data Wizard stores uploaded files on your local disk and uses SQLite to manage your data.
                    </p>
                    <div class="grid md:grid-cols-2 mt-5 gap-4">
                        <a href="{{ config('landing.quick-start-url') }}" class="flex justify-center items-center px-4 py-3 bg-white/90 text-gray-950 rounded-lg font-semibold backdrop-blur-sm hover:bg-white/80 transition-colors border border-white/20 transform hover:scale-105">
                            Full Quick Start Guide
                            <x-icon name="lucide-arrow-right" class="h-4 w-4 inline-block ml-2"/>
                        </a>
                        <a href="{{ config('landing.documentation-url') }}" class="flex justify-center items-center px-4 py-3 bg-white/10 rounded-lg font-semibold backdrop-blur-sm hover:bg-white/20 transition-colors border border-white/20 transform hover:scale-105">
                            View Documentation
                        </a>
                    </div>

                    <div class="flex justify-center gap-2">
                        <a
                            href="{{ config('landing.github-url') }}"
                            class="flex items-center gap-2 text-gray-300 hover:text-cyan-300 transition-colors"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <x-icon name="lucide-github" class="h-5 w-5"/>
                            <span>View on GitHub</span>
                        </a>
                    </div>
                </div>
                 {{-- Diagram with always-on gradient shadow --}}
                <div
                    class="text-center p-6 rounded-2xl bg-black/20 backdrop-blur-lg border border-white/10 shadow-xl"
                    x-data="{ type : 'docker-run' }"
                >
                    <div class="flex items-center gap-3 mb-5">
                        <button
                            @click="type = 'docker-run'"
                            class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-200 hover:bg-white/10 transition-colors"
                            :class="{ 'bg-white/10': type === 'docker-run' }"
                        >
                            Docker Run
                        </button>
                        <button
                            @click="type = 'docker-compose'"
                            class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-200 hover:bg-white/10 transition-colors"
                            :class="{ 'bg-white/10': type === 'docker-compose' }"
                        >
                            Docker Compose
                        </button>

                        <div
                            class="flex-1 flex justify-end"
                            x-data="{
                                copied: false,
                                copy() {
                                    this.copied = true;
                                    this.$clipboard(type === 'docker-run' ? $refs.dockerRun.innerText : $refs.dockerCompose.innerText);
                                    setTimeout(() => {
                                        this.copied = false;
                                    }, 2000);
                                }
                            }"
                        >
                            <x-filament::icon-button
                                icon="lucide-copy"
                                class="ml-auto"
                                color="white"
                                @click="copy"
                                x-show="!copied"
                            />
                            <x-filament::icon-button
                                icon="lucide-check"
                                class="ml-auto"
                                color="success"
                                @click="copy"
                                x-show="copied"
                                x-cloak
                                disabled
                            />
                        </div>
                    </div>
                    <x-content.code
                        language="text"
                        theme="solarized-dark"
                        class="text-left !bg-transparent"
                        x-show="type === 'docker-run'"
                        x-ref="dockerRun"
                    >
                        docker run \
  --name data-wizard \
  -p 9090:80 \
  -p 4430:443 \
  -p 4430:443/udp \
  -v data_wizard_storage:/app/storage \
  -v data_wizard_sqlite_data:/app/database \
  -v data_wizard_caddy_data:/data \
  -v data_wizard_caddy_config:/config \
  -e APP_KEY=[REPLACE_WITH_APP_KEY] \
  mateffy/data-wizard:latest
                    </x-content.code>
                    <x-content.code
                        language="yaml"
                        theme="houston"
                        class="text-left !bg-transparent"
                        x-show="type === 'docker-compose'"
                        x-cloak
                        x-ref="dockerCompose"
                    >
version: '3.8'

services:
  data-wizard:
    name: data-wizard
    image: mateffy/data-wizard:latest
    ports:
      - "9090:80"
      - "4430:443"
      - "4430:443/udp"
    volumes:
      - data_wizard_storage:/app/storage
      - data_wizard_sqlite_data:/app/database
      - data_wizard_caddy_data:/data
      - data_wizard_caddy_config:/config
    environment:
      - APP_KEY=[REPLACE_WITH_APP_KEY]

    volumes:
      data_wizard_storage:
      data_wizard_sqlite_data:
      data_wizard_caddy_data:
      data_wizard_caddy_config:
                        </x-content.code>

                </div>
            </div>
        </div>
    </div>
