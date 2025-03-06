<?php

namespace App\Filament\Pages\LlmSettings;

use App\Filament\Pages\LlmSettings\Concerns\HasApiConnectionDTO;
use App\Models\ApiKey;
use Filament\Infolists\Components\Actions\Action;

class DeleteApiCredentialsAction extends Action
{
    use HasApiConnectionDTO;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Delete API Credentials')
            ->iconButton()
            ->translateLabel()
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                $provider = $this->getApiConnectionDTO()?->provider;

                if ($provider === null) {
                    return;
                }

                ApiKey::query()
                    ->where('provider', $provider)
                    ->delete();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'deleteApiCredentials';
    }
}
