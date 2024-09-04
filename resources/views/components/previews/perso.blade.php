<div class="perso-card-container w-[450px]"
     x-data="{
        rotateX: 0,
        rotateY: 0,
        targetX: 0,
        targetY: 0,
        shineX: 50,
        shineY: 50,
        shineOpacity: 0,
        updateRotation() {
            this.rotateX += (this.targetX - this.rotateX) * 0.1;
            this.rotateY += (this.targetY - this.rotateY) * 0.1;
            this.$refs.card.style.transform = `rotateX(${this.rotateX}deg) rotateY(${this.rotateY}deg)`;
            requestAnimationFrame(this.updateRotation.bind(this));
        }
     }"
     x-init="updateRotation()"
     @mousemove="(e) => {
        const rect = $el.getBoundingClientRect();
        targetY = ((e.clientX - rect.left) / rect.width - 0.5) * 26 / 2;
        targetX = ((e.clientY - rect.top) / rect.height - 0.5) * -26 / 2;
        shineX = ((e.clientX - rect.left) / rect.width) * 100;
        shineY = ((e.clientY - rect.top) / rect.height) * 100;
        shineOpacity = 0.7;
     }"
     @mouseleave="targetX = 0; targetY = 0; shineOpacity = 0">
    <div x-ref="card" class="perso-card bg-gray-50 dark:bg-gray-900 rounded-lg shadow-lg p-6 w-full relative overflow-hidden"
         style="transform-style: preserve-3d; transform: rotateX(0deg) rotateY(0deg);">
        <div class="security-pattern absolute inset-0"></div>
        <div class="shine" :style="`opacity: ${shineOpacity}; transform: translate(${shineX - 30}%, ${shineY - 30}%)`"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <div class="flex items-center mb-2">
                        <div class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-2">
                            <span class="text-xs font-bold">DE</span>
                        </div>
                        <div class="text-sm">
                            <p class="font-bold">BUNDESREPUBLIK DEUTSCHLAND</p>
                            <p class="text-xs">PERSONALAUSWEIS</p>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xl font-bold" x-text="perso.perso_id ? `${perso.perso_id.block1 || ''}${perso.perso_id.block2 || ''}${perso.perso_id.block3 || ''}` : 'N/A'"></p>
                </div>
            </div>

            <div class="flex mb-4">
                <div class="w-1/3">
                    <div class="w-full h-40 bg-gray-200/50 dark:bg-gray-700/50 rounded-lg flex items-center justify-center shadow-inner">
                        <span class="text-gray-500">Photo</span>
                    </div>
                </div>
                <div class="w-2/3 pl-4 text-sm">
                    <p class="mb-1"><span class="font-medium text-gray-500 dark:text-gray-400">Name</span></p>
                    <p class="mb-2" x-text="perso.name"></p>
                    <p class="mb-1"><span class="font-medium text-gray-500 dark:text-gray-400">Geburtstag</span></p>
                    <p class="mb-2" x-text="perso.dateOfBirth || 'N/A'"></p>
                    <p class="mb-1"><span class="font-medium text-gray-500 dark:text-gray-400">Geburtsort</span></p>
                    <p x-text="perso.placeOfRegistration || 'N/A'"></p>
                </div>
            </div>

            <div class="mb-4 text-sm">
                <p class="mb-1"><span class="font-medium text-gray-500 dark:text-gray-400">Staatsangehörigkeit</span></p>
                <p x-text="perso.address && perso.address.country ? perso.address.country : 'N/A'"></p>
            </div>

            <div class="flex justify-between items-end text-sm">
                <div>
                    <p class="mb-1"><span class="font-medium text-gray-500 dark:text-gray-400">Gültig bis</span></p>
                    <p x-text="perso.expirationDate || 'N/A'"></p>
                </div>
                <div class="text-right">
                    <p class="text-sm"><span class="font-medium text-gray-500 dark:text-gray-400">Wohnort/Adresse</span></p>
                    <p x-text="perso.address ? `${perso.address.street || ''}, ${perso.address.postalCode || ''} ${perso.address.city || ''}` : 'N/A'"></p>
                </div>
            </div>

            <div class="mt-4 text-xs">
                <p><span class="font-medium text-gray-500 dark:text-gray-400">Geschlecht:</span> <span x-text="perso.gender || 'N/A'"></span></p>
                <p><span class="font-medium text-gray-500 dark:text-gray-400">Größe:</span> <span x-text="perso.height ? `${perso.height} cm` : 'N/A'"></span></p>
                <p><span class="font-medium text-gray-500 dark:text-gray-400">Gewicht:</span> <span x-text="perso.weight ? `${perso.weight} kg` : 'N/A'"></span></p>
                <p><span class="font-medium text-gray-500 dark:text-gray-400">Augenfarbe:</span> <span x-text="perso.eyeColor || 'N/A'"></span></p>
                <p><span class="font-medium text-gray-500 dark:text-gray-400">Haarfarbe:</span> <span x-text="perso.hairColor || 'N/A'"></span></p>
                <p><span class="font-medium text-gray-500 dark:text-gray-400">Erlaubte Fahrzeuge:</span> <span x-text="perso.allowedVehicles && perso.allowedVehicles.length ? perso.allowedVehicles.join(', ') : 'N/A'"></span></p>
            </div>

{{--            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">--}}
{{--                <p class="text-8xl font-bold text-gray-300 opacity-20">MUSTER</p>--}}
{{--            </div>--}}
        </div>
    </div>
</div>
