<?php

namespace App\Services;

use App\Models\User;
use App\Services\Firebase\FirebaseNotificationService;
use Ramsey\Uuid\Type\Integer;

class pushNotificationService
{
    public function __construct(
        protected FirebaseNotificationService $firebase
    ) {}

    public function sendToUser(
        User $user,
        string $title,
        string $body,
        array $data = []
    ): void {
        if (!$user->fcm_token) {
            return;
        }

        $this->firebase->sendToToken(
            token: $user->fcm_token,
            title: $title,
            body: $body,
            data: $data
        );
    }

    public function sendToToken( string $title,
        string $body,string $token,array $data =[],$id =0)
    {
         if (empty($token)||strlen($token) < 20) {
            return;
        }
        return  $this->firebase->sendToToken(
            token: $token,
            title: $title,
            body: $body,
            data: $data
        );

        

    }
}