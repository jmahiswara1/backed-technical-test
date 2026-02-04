<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthService
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    public function attemptLogin(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

        if (!$user || !$user->password || !Hash::check($password, $user->password)) {
            return ['user' => null, 'token' => null];
        }

        $token = $user->createToken('api')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(): array
    {
        $socialUser = Socialite::driver('google')->stateless()->user();

        $user = $this->users->findByEmail($socialUser->getEmail());

        if (!$user) {
            $user = $this->users->create([
                'id' => (string) Str::uuid(),
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'email' => $socialUser->getEmail(),
                'password' => null,
                'provider_id' => $socialUser->getId(),
                'provider_name' => 'google',
                'role' => 'employee',
            ]);
        } else {
            $user->update([
                'provider_id' => $socialUser->getId(),
                'provider_name' => 'google',
            ]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }
}
