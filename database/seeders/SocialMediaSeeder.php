<?php

namespace Database\Seeders;

use App\Models\SocialMedia;
use Illuminate\Database\Seeder;

class SocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socialMediaPlatforms = [
            [
                'name' => 'Facebook',
                'link' => 'https://www.facebook.com/',
                'icon' => 'fab fa-facebook',
            ],
            [
                'name' => 'Twitter',
                'link' => 'https://www.twitter.com/',
                'icon' => 'fab fa-twitter',
            ],
            [
                'name' => 'Instagram',
                'link' => 'https://www.instagram.com/',
                'icon' => 'fab fa-instagram',
            ],
            [
                'name' => 'YouTube',
                'link' => 'https://www.youtube.com/',
                'icon' => 'fab fa-youtube',
            ],
            [
                'name' => 'LinkedIn',
                'link' => 'https://www.linkedin.com/',
                'icon' => 'fab fa-linkedin',
            ],
            [
                'name' => 'TikTok',
                'link' => 'https://www.tiktok.com/',
                'icon' => 'fab fa-tiktok',
            ],
            [
                'name' => 'Snapchat',
                'link' => 'https://www.snapchat.com/',
                'icon' => 'fab fa-snapchat',
            ],
            [
                'name' => 'WhatsApp',
                'link' => 'https://www.whatsapp.com/',
                'icon' => 'fab fa-whatsapp',
            ],
        ];

        foreach ($socialMediaPlatforms as $platform) {
            SocialMedia::create($platform);
        }
    }
}
