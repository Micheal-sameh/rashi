<?php

namespace App\Console\Commands;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Console\Command;

class GenerateGoogleDriveToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:drive-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Google Drive refresh token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new Client;
        $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_DRIVE_REDIRECT_URI', 'http://localhost'));
        $client->setScopes([Drive::DRIVE_FILE]);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();

        $this->info('Please visit the following URL and authorize the application:');
        $this->line($authUrl);
        $this->newLine();

        $authCode = $this->ask('Enter the authorization code from the redirect URL');

        try {
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            if (isset($accessToken['refresh_token'])) {
                $this->info('Refresh Token: '.$accessToken['refresh_token']);
                $this->newLine();
                $this->info('Add this to your .env file:');
                $this->line('GOOGLE_DRIVE_REFRESH_TOKEN='.$accessToken['refresh_token']);
            } else {
                $this->error('No refresh token received. Make sure to revoke previous access and try again.');
            }
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
        }

        return 0;
    }
}
