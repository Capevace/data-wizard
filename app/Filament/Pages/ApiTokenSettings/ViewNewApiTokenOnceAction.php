<?php

namespace App\Filament\Pages\ApiTokenSettings;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\HtmlString;

class ViewNewApiTokenOnceAction extends Action
{
	public static function getDefaultName(): ?string
	{
		return 'viewNewApiTokenOnce';
	}

	protected function setUp(): void
	{
		parent::setUp();

		$this
			->label('View API Token')
            ->translateLabel()
			->icon('heroicon-o-key')
			->color('gray')
			->modalIcon('heroicon-o-key')
			->modalIconColor('success')
			->modalWidth('lg')
			->modalSubmitAction(false)
			->modalCancelActionLabel(__('Close'))
			->modalDescription('Copy your API key. You will not be able to see it again, so make sure to store it somewhere safe.')
			->fillForm(fn (array $arguments) => [
				'token' => $arguments['token']
			])
			->form([
				TextInput::make('token')
					->label('Token')
                    ->translateLabel()
					->readOnly()
					->hintAction(
						\Filament\Forms\Components\Actions\Action::make('copy')
							->label(__('Copy'))
							->livewireClickHandlerEnabled(false)
							->icon('heroicon-o-clipboard')
							->extraAttributes(fn (string $state) => [
								'x-on:click.prevent' => new HtmlString(<<<JS
								const label = \$el.querySelector('span');
								label.innerHTML = 'Copied ✓';
								setTimeout(() => label.textContent = 'Copy', 2000);
								JS),
								'x-clipboard.raw' => $state
							])
					)
			]);
	}
}
