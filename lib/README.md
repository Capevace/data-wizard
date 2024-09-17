# `Magic` Class

## Chat

```php
use Mateffy\Magic;

/** @var LLM $model */
$model = auth()->user()->getDefaultLLM();


$estates = Magic::extract()
    ->model($model)
    ->file('/path/to/file.pdf')
    ->findMany(Estate::class)
    ->stream();
    
$receipt = Magic::extract()
    ->model($model)
    ->file('/path/to/file.pdf')
    ->find(Receipt::class)
    ->stream();

use Mateffy\Magic;

$messages = Magic::chat()
    ->model('openai/gpt-4')
    ->tools(
        function findWeatherInCity(string $name) {
            $weather = \Weather::find($name);
            
            return $weather->getPlaintextReport();
        }
    )
    ->tools(
        findWeatherInCity: fn (string $name) => Weather::find($name)->getPlaintextReport()
    )
	->tools(
        new SearchMemoryTool,
        new SaveMemoryTool
	)
	->tools([
        new ReadFileTool(dir: '/path/to/files'),
		'readFile' => fn (string $path) => file_get_contents($path),
	])
    ->messages([
        new TextMessage(role: Role::User, content: 'What\'s the weather in Berlin?')
    ])
    ->stream();
    
$messages = Magic::agent()
    ->mission('Refactor the code')
    ->tools(
        new SearchMemoryTool,
        new SaveMemoryTool,
        new ReadFileTool(dir: '/path/to/files'),
        new WriteFileTool(dir: '/path/to/files'),
        function launchChildAgent(string $mission) {
            $childAgent = Magic::agent()
                ->mission($mission)
                ->tools([
                    'readFile' => new ReadFileTool(dir: '/path/to/files'),
                ])
                ->stream();
        },
        log: fn (string $message) => \Log::info($message),
    )
    ->memory($memory);
```
    

## Extracting data

```php

use Capevace\Magic;

// Simplest possible example
$city = Magic::extract()
    ->input('What\'s the weather in Berlin?')
    ->output([
        'type' => 'string',
        'description' => 'The name of the city',
    ])
    ->stream();

// More complex example
$data = Magic::extract()
    ->model('openai/gpt-4')
    ->artifacts([
        LocalArtifact::fromPath('/path/to/file.pdf'),
        LocalArtifact::fromPath('/path/to/file.jpg'),
    ])
    ->schema([
        'type' => 'object',
        'properties' => [
            'name' => [
                'type' => 'string',
                'description' => 'The name of the product',
            ],
            'price' => [
                'type' => 'number',
                'description' => 'The price in EUR',
            ],
        ],
        'required' => ['name'],
    ])
    ->stream();
```

### Copyright and License

This project is made by [Lukas Mateffy](https://mateffy.me) and is licensed under the [GNU Affero General Public License v3.0 (AGPL-3.0)](https://choosealicense.com/licenses/agpl-3.0/).

### Contributing

At the moment, this project is not open for contributions. 
However, if you have ideas, bugs or suggestions, feel free to open an issue or start a discussion!
