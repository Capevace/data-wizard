export type ErrorCallback = (error: Error) => void;

export type TokenStats = {
    tokens: string;
    inputTokens: string;
    outputTokens: string;
    cost: string;
}

export type SourceOptions = {
    onTokenStats?: (tokenStats: TokenStats) => void;
    onProgress?: (message: any) => void;
    onMessage?: (message: any) => void;
    onEnd?: (tokenStats: TokenStats) => void;
    onError?: ErrorCallback;
}

export class Source {
    sourceUrl: string;
    options: SourceOptions;

    eventSource: EventSource;

    messages: any[];
    streamingMessage: any;
    data: any;

    tokenStats: TokenStats;

    constructor(options: SourceOptions) {
        this.options = options;
    }

    connect(sourceUrl: string) {
        this.setupEventSource(new EventSource(sourceUrl));
    }

    protected setupEventSource(eventSource: EventSource) {
        this.eventSource = eventSource;

        this.eventSource.addEventListener('onDataProgress', (event) => {
            this.streamingMessage = JSON.parse(event.data);
            this.data = this.streamingMessage.data;

            this.options.onProgress?.(this.streamingMessage);
        });

        this.eventSource.addEventListener('onData', (event) => {
            const data = JSON.parse(event.data);
            this.messages.push(data);
            this.streamingMessage = null;

            this.options.onMessage?.(data);
        });

        this.eventSource.addEventListener('onErrorMessage', (event) => {
            const data = JSON.parse(event.data);
            const title = data?.error?.title || 'Error';
            const message = data?.error?.message || 'An unknown error occurred.';

            this.messages.push(data);

            this.eventSource.close();
            this.eventSource = null;

            console.error(title, message);

            this.options.onError?.(new Error(`${title}: ${message}`));
        });

        this.eventSource.addEventListener('onTokenStats', (event) => {
            console.log('onTokenStats', event);

            const stats: { [key: string]: any } = JSON.parse(event.data);

            if (stats.tokens) {
                this.tokenStats.tokens = stats.tokens;
            }

            if (stats.input_tokens) {
                this.tokenStats.inputTokens = stats.input_tokens;
            }

            if (stats.output_tokens) {
                this.tokenStats.outputTokens = stats.output_tokens;
            }

            if (stats.cost) {
                const inputCentsPer1k = stats.cost.input_cents_per_1k ?? 0;
                const outputCentsPer1k = stats.cost.output_cents_per_1k ?? 0;

                const inputCostCents = (stats.input_tokens ?? this.tokenStats.inputTokens ?? 0) / 1000 * inputCentsPer1k;
                const outputCostCents = (stats.output_tokens ?? this.tokenStats.outputTokens ?? 0) / 1000 * outputCentsPer1k;

                const totalCostCents = inputCostCents + outputCostCents;
                const totalCostsEuro = totalCostCents / 100;

                console.log('inputCentsPer1k', inputCentsPer1k);
                console.log('outputCentsPer1k', outputCentsPer1k);
                console.log('inputCostCents', inputCostCents);
                console.log('outputCostCents', outputCostCents);
                console.log('totalCostsEuro', totalCostsEuro);

                this.tokenStats.cost = `${totalCostsEuro.toFixed(3)} €`;
            }

            this.options.onTokenStats?.(this.tokenStats);
        });

        this.eventSource.addEventListener('onEnd', (event) => {
            console.log('onEnd', event);
            this.streamingMessage = null;

            this.options.onEnd?.(this.tokenStats);

            this.eventSource.close();
            this.eventSource = null;
        });

        this.eventSource.onerror = (event) => {
            console.error(event);

            this.eventSource.close();
            this.eventSource = null;
        };
    }

    cancel() {
        this.eventSource.close();
        this.eventSource = null;
    }
}
