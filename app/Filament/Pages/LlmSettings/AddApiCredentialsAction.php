<?php

namespace App\Filament\Pages\LlmSettings;

use App\Models\ApiKey;
use App\Models\ApiKey\ApiKeyProvider;
use App\Models\ApiKey\ApiKeyTokenType;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Filament\Infolists\Components\Actions\Action;

class AddApiCredentialsAction extends Action
{
    protected ?ApiKeyProvider $initialProvider = null;

    protected array $disabledProviders = [];

    public function initialProvider(ApiKeyProvider $provider): self
    {
        $this->initialProvider = $provider;

        return $this;
    }

    /**
     * @param  ApiKeyProvider[]  $providers
     */
    public function disabledProviders(array $providers): self
    {
        $this->disabledProviders = $providers;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Configure API')
            ->translateLabel()
            ->icon('heroicon-o-key')
            ->iconPosition('after')
            ->color('primary')
            ->fillForm(fn () => [
                'provider' => $this->initialProvider?->value,
                'fields' => [
                    'token' => '',
                    'organization' => '',
                ],
            ])
            ->modalFooterActionsAlignment('end')
            ->modalSubmitActionLabel(__('Add'))
            ->form(fn () => [
                ToggleButtons::make('provider')
                    ->default($this->initialProvider?->value)
                    ->disableOptionWhen(fn (string $value) => in_array(ApiKeyProvider::from($value), $this->disabledProviders))
                    ->live()
                    ->label('Provider')
                    ->options(ApiKeyProvider::class)
                    ->inline()
                    ->required(),

                Group::make()
                    ->statePath('fields')
                    ->schema(fn (Get $get) => match ($get('provider')) {
                        ApiKeyProvider::OpenAI->value => [
                            TextInput::make('token')
                                ->required()
                                ->label('API Token')
                                ->translateLabel()
                                ->placeholder(__('e.g.').' '.'sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx')
                                ->hint(__('Required')),
                            TextInput::make('organization')
                                ->label('Organization')
                                ->translateLabel()
                                ->hint(__('Optional'))
                                ->placeholder(__('Standard organization')),
                        ],
                        default => [
                            TextInput::make('token')
                                ->required()
                                ->label('API Token')
                                ->translateLabel()
                                ->placeholder(__('e.g.').' '.match ($get('provider')) {
                                    ApiKeyProvider::OpenAI => 'sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                                    default => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                                })
                                ->hint(__('Required')),
                        ],
                    }),
            ])
            ->action(function (array $data) {
                /** @var ApiKeyProvider $provider */
                $provider = ApiKeyProvider::from($data['provider']);

                if ($provider === ApiKeyProvider::OpenAI) {
                    ApiKey::create([
                        'provider' => $provider,
                        'type' => ApiKeyTokenType::Token,
                        'secret' => $data['fields']['token'],
                    ]);

                    if ($data['fields']['organization'] ?? null) {
                        ApiKey::create([
                            'provider' => $provider,
                            'type' => ApiKeyTokenType::Organization,
                            'secret' => $data['fields']['organization'],
                        ]);
                    }
                } else {
                    ApiKey::create([
                        'provider' => $provider,
                        'type' => ApiKeyTokenType::Token,
                        'secret' => $data['fields']['token'],
                    ]);
                }

                $this
                    ->successNotificationTitle(__('API connection created'))
                    ->success();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'addApiCredentials';
    }
}
