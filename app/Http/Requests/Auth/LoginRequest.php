<?php

namespace App\Http\Requests\Auth;

use App\Exceptions\LdapUnavailableException;
use App\Models\User;
use App\Services\LdapAuthenticator;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required_without:email', 'nullable', 'string', 'max:255'],
            'email' => ['required_without:username', 'nullable', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = trim((string) ($this->input('username') ?? $this->input('email')));
        $errorField = $this->filled('username') ? 'username' : 'email';

        if (config('ldap.enabled')) {
            if ($this->authenticateLocalAdministrator($identifier, $errorField)) {
                RateLimiter::clear($this->throttleKey());

                return;
            }

            $this->authenticateWithLdap($identifier, $errorField);

            RateLimiter::clear($this->throttleKey());

            return;
        }

        if (! Auth::attempt([
            'email' => $identifier,
            'password' => $this->input('password'),
        ], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                $errorField => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    private function authenticateLocalAdministrator(string $identifier, string $errorField): bool
    {
        if (! filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($identifier)])
            ->where('role', User::ROLE_ADMINISTRATOR)
            ->first();

        if (! $user) {
            return false;
        }

        if (! Hash::check((string) $this->input('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                $errorField => trans('auth.failed'),
            ]);
        }

        Auth::login($user, $this->boolean('remember'));

        return true;
    }

    private function authenticateWithLdap(string $identifier, string $errorField): void
    {
        try {
            $profile = app(LdapAuthenticator::class)->authenticate(
                $identifier,
                (string) $this->input('password'),
            );
        } catch (LdapUnavailableException $exception) {
            Log::error('LDAP authentication service is unavailable.', [
                'error_code' => $exception->getCode(),
            ]);

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                $errorField => 'Não foi possível conectar ao serviço de autenticação. Tente novamente mais tarde.',
            ]);
        }

        if ($profile === null) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                $errorField => trans('auth.failed'),
            ]);
        }

        $user = User::query()->where('username', $profile['username'])->first();

        if (! $user) {
            $user = User::query()
                ->whereRaw('LOWER(email) = ?', [$profile['email']])
                ->first();
        }

        if ($user && $user->username && $user->username !== $profile['username']) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                $errorField => trans('auth.failed'),
            ]);
        }

        if (! $user) {
            $user = User::query()->create([
                'name' => $profile['name'],
                'email' => $profile['email'],
                'username' => $profile['username'],
                'password' => Str::random(64),
                'email_verified_at' => now(),
                'role' => User::ROLE_USER,
            ]);
        } else {
            $user->forceFill([
                'username' => $profile['username'],
                'name' => $profile['name'],
            ])->save();
        }

        Auth::login($user, $this->boolean('remember'));
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $identifier = $this->input('username', $this->input('email', ''));

        return Str::transliterate(Str::lower((string) $identifier).'|'.$this->ip());
    }
}
