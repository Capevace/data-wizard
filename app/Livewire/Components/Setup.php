<?php

namespace App\Livewire\Components;

use App\Filament\Pages\Dashboard;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\SimplePage;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

/**
 * @property-read string $generated_environment
 * @property-read bool $show_environment
 */
class Setup extends SimplePage implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'livewire.setup';

    public const MAX_STEPS = 2;

    #[Locked]
    public int $step = 0;

    public array $data = [];

    public function mount()
    {
        // Do not ever show this page if there is already a user.
        if (User::query()->exists()) {
            $this->redirect(route('filament.app.auth.login'));

            // If for some reason the redirect doesn't work, we'll abort here
            abort(404);
        }

        $this->form->fill([
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);
    }

    #[Computed(seconds: 60, cache: true)]
    public function generated_environment(): string
    {
        $generated_app_key = 'base64:' . base64_encode(Encrypter::generateKey(config('app.cipher')));

        return <<<EOT
        APP_KEY="{$generated_app_key}"
        EOT;
    }

    #[Computed]
    public function show_environment(): bool
    {
        return env('APP_KEY') === null;
    }

    public function next()
    {
        $this->step++;

        if ($this->step >= self::MAX_STEPS) {
            $this->step = self::MAX_STEPS;
        }
    }

    public function previous()
    {
        $this->step--;

        if ($this->step < 0) {
            $this->step = 0;
        }
    }

    public function finish()
    {
        $data = $this->form->getState();

        // Failsafe to prevent this from being run if there is already a superadmin.
        if (User::query()->exists()) {
            return;
        }

        $user = User::create([
            'name' => 'Superadmin',
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        auth()->login($user);

        $this->redirect(Dashboard::getUrl(panel: 'app'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->maxLength(255)
                    ->email()
                    ->required()
                    ->placeholder('me@example.com'),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->minLength(8)
                    ->maxLength(255)
                    ->revealable()
                    ->confirmed()
                    ->required()
                    ->placeholder('********'),

                TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->minLength(8)
                    ->maxLength(255)
                    ->revealable()
                    ->required()
                    ->placeholder('********'),
            ]);
    }
}
