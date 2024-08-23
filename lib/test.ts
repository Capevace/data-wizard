// @anthropic-ai/sdk
import Anthropic from '@anthropic-ai/sdk';

const anthropic = new Anthropic();

async function main() {
  const stream = await anthropic.messages.create({
    max_tokens: 1024,
    messages: [{ role: 'user', content: 'Hello, tell me a short story about a computer scientist, but with a twist.' }],
    model: 'claude-3-5-sonnet-20240620',
    stream: true,
    tools: [
      {
        name: 'echo_story',
        description: 'Shows a story to the user.',
        input_schema: {
          type: 'object',
          properties: {
            story: {
              type: 'string',
              description: 'The story to show to the user.'
            }
          }
        }
      }
    ],
    tool_choice: {
      type: 'tool',
      name: 'echo_story'
    }
  });

  for await (const messageStreamEvent of stream) {
    console.log(messageStreamEvent);
  }
}

main();