<?php

namespace App\Services;

use GuzzleHttp\Client;

class ZoomService
{
    protected $clientId;
    protected $clientSecret;
    protected $accountId;
    protected $token;
    protected $tokenExpiresAt;

    public function __construct()
    {
        $this->clientId = config('services.zoom.client_id');
        $this->clientSecret = config('services.zoom.client_secret');
        $this->accountId = config('services.zoom.account_id');
    }

    public function getAccessToken()
    {
        if ($this->token && $this->tokenExpiresAt > time()) {
            return $this->token;
        }

        $client = new Client();

        $response = $client->post('https://zoom.us/oauth/token', [
            'auth' => [$this->clientId, $this->clientSecret],
            'query' => [
                'grant_type' => 'account_credentials',
                'account_id' => $this->accountId,
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        $this->token = $data['access_token'];
        $this->tokenExpiresAt = time() + $data['expires_in'];

        return $this->token;
    }

    public function createMeeting($userId, $topic, $startTime, $duration = 30)
    {
        $token = $this->getAccessToken();

        $client = new Client();

        $response = $client->post("https://api.zoom.us/v2/users/{$userId}/meetings", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'topic' => $topic,
                'type' => 2,
                'start_time' => $startTime, // بصيغة ISO8601: '2023-05-30T15:00:00Z'
                'duration' => $duration,
                'timezone' => 'UTC',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
