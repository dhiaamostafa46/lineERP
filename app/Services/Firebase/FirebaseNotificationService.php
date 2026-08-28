<?php

namespace App\Services\Firebase;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

class FirebaseNotificationService
{
    protected string $projectId;
    protected string $credentialsPath;
    protected Client $client;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
        $this->credentialsPath = config('firebase.credentials');
        $this->client = new Client();
    }

    protected function getAccessToken(): string
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        $credentials = new ServiceAccountCredentials(
            $scopes,
            $this->credentialsPath
        );

        $token = $credentials->fetchAuthToken();

        return $token['access_token'];
    }

    public function sendToToken(
        string $token,
        string $title,
        string $body,
        array $data = []
    ): array {
        try {
        $accessToken = $this->getAccessToken();

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => collect($data)->map(fn($value) => (string) $value)->toArray(),
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        return json_decode($response->getBody()->getContents(), true);

        } catch (\GuzzleHttp\Exception\ClientException $e) { 
           return [
            'success' => false,
            'error' => $e->getMessage(),
             ];
        }
    }
}