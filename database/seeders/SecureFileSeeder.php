<?php

namespace Database\Seeders;

use App\Models\SecureFile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SecureFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $credentials = [
            "type" => "service_account",
            "project_id" => "waste-pilot-arcann-hegemonic",
            "private_key_id" => "2637c1092d971fdc2a7c40040c519ca4b455afe3",
            "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhki...\n-----END PRIVATE KEY-----\n",
            "client_email" => "firebase-adminsdk-zmc1q@waste-pilot-arcann-hegemonic.iam.gserviceaccount.com",
            "client_id" => "104171079534193237891",
            "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
            "token_uri" => "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-zmc1q%40waste-pilot-arcann-hegemonic.iam.gserviceaccount.com",
            "universe_domain" => "googleapis.com",
        ];

        // Save in DB
        SecureFile::updateOrCreate(
            ['code' => 'FCM_CREDENTIALS'],
            [
                'credentials'  => json_encode($credentials),
                'content_type' => 'fcm',
            ]
        );

        // Save in storage/app/public/firebase/firebase.json
        // $filePath = 'firebase/firebase.json';

        // if (!Storage::disk('public')->exists($filePath)) {
        //     $json = json_encode($credentials, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        //     Storage::disk('public')->put($filePath, $json);
        // }
    }
}
