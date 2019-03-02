<?php

namespace App\Interfaces;

interface PushNotificationInterface {

    public function send($deviceIdentifier, $message, $data, $badge);
}
