import anthropic

client = anthropic.Anthropic()

with client.messages.stream(
    max_tokens=1024,
    messages=[{"role": "user", "content": "Output some example json"}],
    model="claude-3-opus-20240229",
) as stream:
  for text in stream.text_stream:
      print(text, end="", flush=True)
