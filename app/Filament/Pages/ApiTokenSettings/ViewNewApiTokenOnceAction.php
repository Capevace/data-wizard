<?php

namespace App\Filament\Pages\ApiTokenSettings;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
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
			->infolist(fn (Infolist $infolist, array $arguments) => $infolist
                ->state([
                    'token' => $arguments['token'],
                ])
                ->schema([
                    TextEntry::make('token')
                        ->label('Token')
                        ->hint('Click to copy')
                        ->translateLabel()
                        ->copyable()
                ])
            );
	}
}
