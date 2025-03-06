<?php

namespace App\Filament\Pages\ApiTokenSettings;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Laravel\Sanctum\PersonalAccessToken;

class RenameApiTokenAction extends Action
{
	public static function getDefaultName(): ?string
	{
		return 'renameApiToken';
	}

	protected function setUp(): void
	{
		parent::setUp();

		$this
			->label('Rename API Token')
            ->translateLabel()
			->icon('heroicon-o-pencil-square')
			->color('gray')
			->modalHeading(__('Rename API Token'))
			->modalIcon('heroicon-o-pencil-square')
			->modalWidth('lg')
			->modalSubmitActionLabel(__('Rename'))
			->fillForm(fn (PersonalAccessToken $record) => [
				'name' => $record->name,
			])
			->form([
				TextInput::make('name')
					->label('Label')
                    ->translateLabel()
					->placeholder('e.g. Production access')
					->maxLength(255)
					->required()
			])
			->successNotificationTitle(__('Renamed'))
			->action(function (array $data, PersonalAccessToken $record) {
                $record->name = $data['name'];
                $record->save();

                $this->success();
            });
	}
}
