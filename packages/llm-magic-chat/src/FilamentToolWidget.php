<?php

namespace Mateffy\Magic\Chat;

use App\Domains\FacilityManagement\Models\RepairTicket;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Filament\Livewire\GlobalSearch;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Mateffy\Magic\Chat\Livewire\FakeLivewire;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\Functions\MagicFunction;
use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;

class FilamentToolWidget
{
    public function __construct(
        /** @var class-string<Resource> $resource */
        protected string $resource,
        /** @var class-string<ListRecords> $list */
        protected string $list,
		/** @var class-string<CreateRecord> $create */
        protected string $create,
        /** @var class-string<ViewRecord|EditRecord> $view */
        protected string $view
    )
    {

    }

    protected function getFilters(): Collection
    {
        $component = Livewire::new(FakeLivewire::class);
        $component->resource = $this->resource;
        $table = Table::make($component);
        $table = $component::table($table);


        $parse = function (Filter|SelectFilter|TrashedFilter|QueryBuilder $filter) {
            [$schema, $required] = $this->schemafy($filter->getFormSchema());

            return match ($filter::class) {
                QueryBuilder::class => [

                ],
                SelectFilter::class => [
                    "filter_{$filter->getName()}" => [
                        'type' => 'string',
                        'enum' => array_keys($filter->getOptions()),
                    ]
                ],
                TrashedFilter::class => [
                    "filter_{$filter->getName()}" => [
                        'type' => 'boolean',
                    ]
                ],
                default => [
                    "filter_{$filter->getName()}" => array_filter([
                        'type' => 'object',
                        'properties' => $schema,
                        'required' => count($required) > 0 ? $required : null,
                    ])
                ]
            };
        };

        /** @var Table $table */
        $filters = collect($table->getFilters())
            ->mapWithKeys($parse);

        return $filters;
    }

	protected function getInitialDataSchema(): array
	{
		$component = Livewire::new(FakeLivewire::class);
        $component->resource = $this->resource;

        $form = Form::make($component);
        $form = $component->form($form);
		/** @var \Filament\Forms\Form $form **/

		[$schema, $required] = $this->schemafy($form->getComponents());

		return [
			'type' => 'object',
			'properties' => $schema,
//			'required' => $required
		];
	}

	protected function schemafy(array $components): array
	{
		$schema = [];
		$required = [];

		foreach ($components as $component) {
			/** @var Component $component */

			if (!method_exists($component, 'getName')) {
				[$merge_schema, $merge_required] = $this->schemafy($component->getChildComponents());

				$schema = [
					...$schema,
					...$merge_schema
				];

				$required = [
					...$required,
					...$merge_required
				];

				continue;
			}

			$name = $component->getName();

			if ($component instanceof Field && $component->isRequired()) {
				$required[] = $name;
			}

			$schema[$name] = match ($component::class) {
				Grid::class, Section::class, Fieldset::class => [
					'type' => 'object',
					'properties' => $this->schemafy($component->getChildComponents())
				],
                DatePicker::class => [
                    'type' => 'string',
                    'format' => 'date',
                ],
                DateTimePicker::class => [
                    'type' => 'string',
                    'format' => 'date-time',
                ],
                TimePicker::class => [
                    'type' => 'string',
                    'format' => 'time',
                ],
				default => [
					'type' => 'string'
				]
			};
		}

		return [$schema, $required];
	}

    public function tools(): array
    {
        $name = str($this->resource::getModel())
            ->afterLast('\\');

        $filters = $this->getFilters();
		$initialDataSchema = $this->getInitialDataSchema();

        return [
            "find{$name}" => new MagicFunction(
                name: "find{$name}",
                schema: [
                    'type' => 'object',
                    'properties' => [
                        'search' => [
                            'type' => 'string',
                        ],
                        ...$filters,
                    ],
                ],
                callback: function (FunctionCall $call) {
                    $models = $this->resource::getModel()::search($call->arguments['search'])
                        ->get()
                        ->load(['buildings', 'saved_locations', 'buildings.rentables', 'buildings.features', 'buildings.floorplans.areas.features']);

                    return FunctionOutputMessage::output(
                        call: $call,
                        output: [
                            'status' => 'Successfully executed! These records are now visible to the user in table.',
                            'count' => $models->count(),
                            'record_ids' => $models
                                ->pluck('name', 'id')
                                ->toArray(),
                            'data' => $models
                                ->map(fn (Model $model) => json_encode($model->toArray()))
    //                            ->map(fn (Model $model) => Str::endsWith($model::class, 'Estate')
    //                                ? (new EstateSummary(estate: $estate))->format()
    //                                : $model->toArray()
    //                            )
                                ->join("\n\n")
                        ]
                    );
                }
            ),
            /**
             * @description Use the search argument to show subsets of records! Always use the `search` argument, unless there's a specific filter for what you want filtered (example: dates).
             */
            "list{$name}" => new MagicFunction(
                name: "list{$name}",
                schema: [
                    'type' => 'object',
                    'properties' => [
                        'search' => [
                            'type' => 'string',
                        ],
                        'limit' => [
                            'type' => 'integer',
                            'minimum' => 1,
                            'maximum' => 100,
                        ],
                        'offset' => [
                            'type' => 'integer',
                            'minimum' => 0,
                        ],
                        ...$filters,
                    ],
                ],
                callback: fn (FunctionCall $call) => FunctionOutputMessage::output(
                    call: $call,
                    output: [
                        ...$call->arguments,
						'status' => 'Successfully executed! These records are now visible to the user in table.',
						'records' => $this->resource::getModel()::query()->limit(10)->get()->toArray(),
					]
                )
            ),
			"create{$name}" => new MagicFunction(
                name: "create{$name}",
                schema: [
                    'type' => 'object',
                    'properties' => [
                        'initial_data' => $initialDataSchema
                    ],
                ],
                callback: fn (FunctionCall $call) => FunctionOutputMessage::output(
                    call: $call,
                    output: 'Successfully executed! The user now sees a form to create a new model'
                )
            ),
            "view{$name}" => new MagicFunction(
                name: "view{$name}",
                schema: [
                    'type' => 'object',
                    'required' => ['id'],
                    'properties' => [
                        'id' => [
                            'type' => 'string',
                            'description' => 'The ID of the record to view',
                        ]
                    ],
                ],
                callback: fn (FunctionCall $call) => FunctionOutputMessage::output(
                    call: $call,
                    output: 'Successfully executed! The user now sees the model data'
                )
            ),
        ];
    }

    public function toolWidgets(): array
    {
        $name = str($this->resource::getModel())
            ->afterLast('\\');

        return [
            "find{$name}" => ToolWidget::closure(function (InvokableFunction $tool, FunctionInvocationMessage $invocation, ?FunctionOutputMessage $output) {
				if ($output === null) {
					return ToolWidget::loading()->render($tool, $invocation, $output);
				}

                return ToolWidget::livewire(
                    fn () => $this->list,
                    [
                        'initial_ids' => collect(Arr::get($output->data(), 'record_ids'))
                            ->keys()
                            ->all()
                    ]
                )->render($tool, $invocation, $output);
			}),

            "list{$name}" => ToolWidget::livewire(
                fn () => $this->list,
                fn (FunctionInvocationMessage $invocation) => [
                    'success' => true,
                    'tableSearch' => Arr::get($invocation->data() ?? [], 'search'),
                    'limit' => Arr::get($invocation->data() ?? [], 'limit'),
                    'offset' => Arr::get($invocation->data() ?? [], 'offset'),
                    'tableFilters' => $this->getFilters()
                        ->map(fn (array $filter, string $name) => $name)
                        ->mapWithKeys(function (string $filter) use ($invocation) {
                            $data = $invocation->data()[$filter] ?? null;

                            return [
                                Str::after($filter, 'filter_') => is_array($data)
                                    ? array_filter($data)
                                    : array_filter([$data])
                            ];
                        })
                        ->filter()
                        ->toArray(),
                ]
            ),
			"create{$name}" => ToolWidget::livewire(
                fn () => $this->create,
                fn (FunctionInvocationMessage $invocation) => [
                    'success' => true,
                    'data' => Arr::get($invocation->data() ?? [], 'initial_data'),
                ]
            ),
            "view{$name}" => ToolWidget::livewire(
                fn () => $this->view,
                fn (FunctionInvocationMessage $invocation) => [
                    'success' => true,
                    'record' => Arr::get($invocation->data() ?? [], 'id'),
                ]
            ),
        ];
    }
}
