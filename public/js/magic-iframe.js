window.Magic = window.Magic ?? {
    url: 'https://data-wizard.ai',

    iframe: {
        url({ extractorId, bucketId = null, runId = null, signature = null, step = null }) {
            console.log('Magic iFrame URL', `${window.Magic.url}/embed/${extractorId}`);
            const url = new URL(`${window.Magic.url}/embed/${extractorId}`);

            if ((bucketId || runId) && !signature) {
                throw new Error('A signature is required when providing a bucketId or runId');
            }

            if (bucketId) {
                url.searchParams.append('bucketId', bucketId);
            }

            if (runId) {
                url.searchParams.append('runId', runId);
            }

            if (signature) {
                url.searchParams.append('signature', signature);
            }

            if (step) {
                url.searchParams.append('step', step);
            }

            return url.toString();
        },

        onEvent(type, callback) {
            const listener = (event) => {
                if (typeof event.data === 'object' && event.data.type === type) {
                    callback(event.data);
                }
            };

            window.addEventListener('message', listener);

            return () => {
                window.removeEventListener('message', listener);
            };
        },

        onResize(callback) {
            return this.onEvent('magic-iframe-resize', (message) => callback(message));
        },

        onSubmit(callback) {
            return this.onEvent('magic-iframe-submit', (message) => callback(message.data));
        },

        autoHeight(iframe) {
            return this.onResize(({ height }) => {
                iframe.style.height = height + 2 + 'px';
            });
        },

        create(url, { container = null, iframe = null, keepBorder = false, autoHeight = true } = {}) {
            const iframeElement = iframe ?? document.createElement('iframe');
            iframeElement.setAttribute('x-ref', 'iframe');
            iframeElement.src = url;
            iframeElement.style.width = '100%';

            if (!keepBorder) {
                iframeElement.style.border = 'none';
            }

            if (autoHeight) {
                iframeElement.style.height = '600px';

                this.autoHeight(iframeElement);
            }

            if (container) {
                container.appendChild(iframeElement);
            }

            return iframeElement;
        }
    }
};
