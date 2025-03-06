<?php

namespace App\Filament\Pages\ApiTokenSettings;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;

class IssueNewApiTokenAction extends Action
{
	public static function getDefaultName(): ?string
	{
		return 'issueNewApiToken';
	}

	protected function setUp(): void
	{
		parent::setUp();

		$this
			->label('Issue New API Token')
            ->translateLabel()
			->icon('heroicon-o-key')
			->color('primary')
			->modalIcon('heroicon-o-key')
			->modalWidth('lg')
			->modalSubmitActionLabel('Create')
			->modalDescription('Create a new API token to access the REST API.')
			->form([
				TextInput::make('name')
					->label('Label')
					->placeholder('e.g. Production access')
					->maxLength(255)
					->required()
			])
			->action(function (array $data) {
                $user = auth()->user();

                $token = $user->createToken($data['name']);

                $this->getLivewire()->mountAction('viewNewApiTokenOnce', ['token' => $token->plainTextToken]);
            });
	}
}
