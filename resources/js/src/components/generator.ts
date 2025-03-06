import type { Alpine } from 'alpinejs';
import {Source, TokenStats} from "./generator/source";

const emptyTokenStats: TokenStats = { tokens: '–', inputTokens: '–', outputTokens: '–', cost: '–' };

interface IGenerator {
	source: Source | null;
    messages: any[];
    streamingMessage: any | null;
	tokenStats: typeof emptyTokenStats;

	init(): void;
    stream(): void;
}

export function GeneratorComponent({ sourceUrl, debugModeEnabled }: { sourceUrl?: string, debugModeEnabled?: boolean } = {}) {
	return {
        tab: 'gui',
        source: null,
        debugModeEnabled: debugModeEnabled ?? false,

        messages: [],
        streamingMessage: null,

        tokenStats: { ...emptyTokenStats } as TokenStats,

        data: null,

		init() {
			console.log('init');

            this.$wire.$watch('actorTab', (actorTab) => {
                if (actorTab) {
                    this.tab = 'chat';
                } else {
                    this.tab = initialTab;
                }
            });
        },

        startStreaming(sourceUrl: string) {
            this.messages = [];
            this.streamingMessage = null;
            this.tokenStats = {...emptyTokenStats};
            this.source = new Source({
                onProgress: (message) => {
                    console.log('onProgress', message);

                    this.streamingMessage = message;
                    this.data = message.data;
                },
                onMessage: (message) => {
                    console.log('onMessage', message);

                    this.messages.push(message);
                },
                onTokenStats: (tokenStats) => {
                    console.log('onTokenStats', tokenStats);

                    this.tokenStats = tokenStats;
                },
                onEnd: (tokenStats) => {
                    console.log('onEnd', tokenStats);

                    this.tokenStats = tokenStats;
                },
                onError: (error) => {
                    console.error('onError', error);

                    window.alert(error.message);
                }
            });
        }
	};
}

export default function GeneratorPlugin(Alpine: Alpine) {
	Alpine.data('GeneratorComponent', GeneratorComponent);
}
