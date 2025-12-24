<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Http;

trait ArAuthentication
{
    protected function arKey(): string
    {
        $key = env('AR_SECRET_KEY');

        return base64_encode($key);
    }

    protected function BaseUrl(): string
    {
        return env('AR_BASE_URL');
    }

    protected function arLogin($membership_code, $password)
    {
        $response = Http::post($this->BaseUrl().'login', [
            'secret_key' => $this->arKey(),
            'membership_code' => $membership_code,
            'password' => $password,
        ]);

        if ($response->failed()) {
            return back()->withErrors([
                'login_error' => $response->toPsrResponse()->getReasonPhrase(),
            ])->withInput();
        }
        $data = $response->json()['data'];

        $user = $this->findOrCreateUser($data);

        return $user;
    }

    private function findOrCreateUser(array $data): User
    {
        $repo = $this->userService;

        return $repo->updateOrCreate($data);
    }

    protected function ArQrlogin($qr_code)
    {
        $response = Http::post($this->BaseUrl().'qr-login', [
            'secret_key' => $this->arKey(),
            'qr_code' => $qr_code,
        ]);

        if ($response->failed()) {
            return $response->json();
        }
        $data = $response->json()['data'];

        $user = $this->findOrCreateUser($data);

        return $user;
    }

    public function resetPassword($email)
    {
        $response = Http::post($this->BaseUrl().'reset-password-link', [
            'secret_key' => $this->arKey(),
            'email' => $email,
        ]);

        return $response;
    }
}
