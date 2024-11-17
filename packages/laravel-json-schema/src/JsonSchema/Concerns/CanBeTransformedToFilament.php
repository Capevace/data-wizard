<?php

namespace Mateffy\JsonSchema\Concerns;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mateffy\JsonSchema;
use Mateffy\JsonSchema\Exceptions\InvalidType;

trait CanBeTransformedToFilament
{
	public function toFilamentFormSchema(): array
	{
        if ($this->satisfies('object')) {
            return $this->properties()
                ->map(function (array $property, string $key) {
                    return JsonSchema::from($property)->toFilamentFormComponent($key);
                })
                ->values()
                ->all();
        }

        return [
            $this->toFilamentFormComponent('value')
        ];
	}


	public function toFilamentFormComponent(string $name): Component
	{
        $getTitle = fn () => Arr::get($this->schema, 'magic_ui.label')
            ?? Arr::get($this->schema, 'title')
            ?? str($name)->replace('_', ' ')->title();

        $getDescription = fn () => Arr::get($this->schema, 'magic_ui.description')
            ?? Arr::get($this->schema, 'description');

        $getPlaceholder = fn () => Arr::get($this->schema, 'magic_ui.placeholder');

        $isHidden = fn () => Arr::get($this->schema, 'magic_ui.hidden');

		if ($items = $this->items()) {
            /** @var class-string<Repeater> $repeaterClass */
            $repeaterClass = config('json-schema.filament.repeater');

            $repeater = $repeaterClass::make($name)
                ->hidden($isHidden)
                ->label($getTitle)
                ->helperText($getDescription);

            if ($items->isIterable()) {
                $repeater->schema($items->toFilamentFormSchema());
            } else {
                $repeater->simple($items->toFilamentFormComponent('value'));
            }

            return $repeater;
        }

        if ($this->satisfies('object')) {
            /** @var class-string<Grid> $grid */
            $grid = config('json-schema.filament.grid');

            return $grid::make($name)
                ->hidden($isHidden)
                ->label($getTitle)
                ->schema($this->toFilamentFormSchema());
        }

        if ($this->satisfiesAny(['string', 'number', 'integer'])) {
            /** @var class-string<TextInput> $inputClass */
            $inputClass = config('json-schema.filament.text-input');

            $input = $inputClass::make($name)
                ->hidden($isHidden)
                ->label($getTitle)
                ->placeholder($getPlaceholder)
                ->helperText($getDescription);

            if ($this->get('format') === 'email') {
                $input->email();
            }

            if ($this->satisfiesAny(['number', 'integer'])) {
                $input
                    ->numeric()
                    ->step($this->satisfies('integer') ? 1 : null)
                    ->minValue($this->get('minimum'))
                    ->maxValue($this->get('maximum'));
            }

            if ($this->satisfies('string')) {
                $input->maxLength($this->get('maxLength'));
                $input->minLength($this->get('minLength'));
            }

            return $input;
        }

        if ($this->satisfies('boolean')) {
            /** @var class-string<Toggle> $toggle */
            $toggle = config('json-schema.filament.toggle');

            return $toggle::make($name)
                ->hidden($isHidden)
                ->label($getTitle)
                ->helperText($getDescription);
        }

        Log::warning("Unsupported Filament Component type: " . implode(', ', $this->type()));

        return Hidden::make($name);
	}

	public function toFilamentTableColumns(?string $prefix = null): array
	{
        $prefixName = fn (string $name) => implode('.', array_filter([$prefix, $name]));

        if ($this->satisfies('object')) {
            return $this->properties()
                ->map(fn (array $property, string $name) =>
                    JsonSchema::from($property)
                        ->toFilamentTableColumn($prefixName($name))
                )
                ->values()
                ->all();
        }

        return [
            $this->toFilamentTableColumn($prefixName('value'))
        ];
	}

	public function toFilamentTableColumn(string $name): Column
	{
		$getTitle = fn () => Arr::get($this->schema, 'magic_ui.label')
            ?? Arr::get($this->schema, 'title')
            ?? str($name)->replace('_', ' ')->title();

        $getDescription = fn () => Arr::get($this->schema, 'magic_ui.description')
            ?? Arr::get($this->schema, 'description');

        $getPlaceholder = fn () => Arr::get($this->schema, 'magic_ui.placeholder');

        $hiddenByDefault = match (Str::afterLast($name, '.')) {
            'name', 'title', 'label' => false,
            default => true,
        };


        if ($this->satisfies('boolean')) {
            /** @var class-string<IconColumn> $iconColumn */
            $iconColumn = config('json-schema.filament.table.icon-column');

            return $iconColumn::make($name)
                ->toggleable()
                ->toggledHiddenByDefault($hiddenByDefault)
                ->label($getTitle);
        }

        /** @var class-string<TextColumn> $textColumn */
        $textColumn = config('json-schema.filament.table.text-column');

        $column = $textColumn::make($name)
            ->toggleable()
            ->toggledHiddenByDefault($hiddenByDefault)
            ->hidden(fn () => $this->satisfiesAny(['array', 'object']))
            ->label($getTitle)
//            ->formatStateUsing(fn ($state) => str($state)
//                ->stripTags()
//                ->limit(80)
//                ->toHtmlString()
//            )
            ->placeholder($getPlaceholder);

        return $column;
	}

	public function toFilamentInfolistSchema(): array
	{
        if ($this->satisfies('object')) {
            return $this->properties()
                ->map(function (array $property, string $name) {
                    return JsonSchema::from($property)->toFilamentInfolistComponent($name);
                })
                ->values()
                ->all();
        }

        return [
            $this->toFilamentInfolistComponent('value')
        ];
	}

	public function toFilamentInfolistComponent(string $name)
	{
        $getTitle = fn () => Arr::get($this->schema, 'magic_ui.label')
            ?? Arr::get($this->schema, 'title')
            ?? str($name)->replace('_', ' ')->title();

        $getDescription = fn () => Arr::get($this->schema, 'magic_ui.description')
            ?? Arr::get($this->schema, 'description');

        $getPlaceholder = fn () => Arr::get($this->schema, 'magic_ui.placeholder');

        if ($this->satisfies('object')) {
            /** @var class-string<\Filament\Infolists\Components\Grid> $grid */
            $grid = config('json-schema.filament.infolist.grid');

            return $grid::make($name)
                ->label($getTitle)
                ->schema($this->toFilamentFormSchema());
        }

        if ($this->satisfies('boolean')) {
            /** @var class-string<\Filament\Infolists\Components\IconEntry> $iconEntry */
            $iconEntry = config('json-schema.filament.infolist.icon-entry');

            return $iconEntry::make($name)
                ->label($getTitle)
                ->helperText($getDescription);
        }

        /** @var class-string<\Filament\Infolists\Components\TextEntry> $textEntry */
        $textEntry = config('json-schema.filament.infolist.text-entry');

        return $textEntry::make($name)
            ->label($getTitle)
            ->helperText($getDescription);
	}
}
