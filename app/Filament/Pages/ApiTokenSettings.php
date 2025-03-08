<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Pages\ApiTokenSettings\IssueNewApiTokenAction;
use App\Filament\Pages\ApiTokenSettings\RenameApiTokenAction;
use App\Filament\Pages\ApiTokenSettings\ViewNewApiTokenOnceAction;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ApiTokenSettings extends Page implements HasTable
{
    protected static ?string $cluster = Settings::class;
	protected static ?string $slug = 'api-tokens';
	protected static string $view = 'filament.pages.api-token-settings';

	use InteractsWithTable;

	public function getTitle(): string|Htmlable
	{
		return 'Personal API Tokens';
	}

    public static function getNavigationLabel(): string
    {
        return 'Personal API Tokens';
    }


    public function table(Table $table): Table
	{
		$user = auth()->user();

		return $table
			->query(fn () => $user->tokens())
			->columns([
				TextColumn::make('name')
					->label('Label'),

				TextColumn::make('created_at')
					->label('Created At')
					->since(),

                TextColumn::make('updated_at')
                    ->label('Last Used At')
                    ->since(),
			])
			->headerActions([
				IssueNewApiTokenAction::make()
			])
			->bulkActions([])
			->actions([
				RenameApiTokenAction::make(),
				DeleteAction::make(),
			])
            ->paginated(false)
			->emptyStateIcon('heroicon-o-key')
			->emptyStateHeading('No API Tokens')
			->defaultSort('name');
	}

	public function viewNewApiTokenOnceAction(): Action
	{
		return ViewNewApiTokenOnceAction::make('viewNewApiTokenOnce');
	}
}
