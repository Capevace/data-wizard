from litellm import completion
import os

messages = [{ "content": "Tell a short story about a computer scientist with a twist","role": "user"}]

tools = [
{
    "type": "function",
    "function": {
        "name": "echo_story",
        "description": "Shows a story to the user.",
        "parameters": {
            "type": "object",
            "properties": {
                "story": {
                    "type": "string",
                    "description": "The story to show to the user."
                }
            },
            "required": ["story"]
        },
    },
}
]

response = completion(
    'claude-3-5-sonnet-20240620',
    messages,
    stream=True,
    tools=tools,
    tool_choice='echo_story',
)

for part in response:
    print(part.choices[0].delta)