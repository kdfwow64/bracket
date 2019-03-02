<?php

namespace App\Repository;

use App\Interfaces\PushNotificationInterface;

/**
 *  IosPushRepository to send push on IOS device
 */
class IosPushRepository implements PushNotificationInterface {

    private $used;

    function __construct($server = 'sandbox') {
        $this->used = ($server == 'sandbox') ? config('pushnotification.apple.sandbox') : config('pushnotification.apple.production');
    }

    /**
     * method to send IOS push
     * @param string $device_token
     * @param string $message
     * @param array $data
     * @param int $badge
     * @return true
     */
    public function send($device_token, $message, $data, $badge) {

        try {
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $this->used['pem_file']);
            stream_context_set_option($ctx, 'ssl', 'passphrase', $this->used['passphrase']);
            // Open a connection to the APNS server
            $fp = stream_socket_client(
                    $this->used['url'], $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            if (!$fp) {
                return false;
            }

            if (!isset($data['silent_flag'])) {
                // non silent
                $body['aps'] = array(
                    'alert' => array(
                        'title' => $data['title'],
                        'body' => $message
                    ),
                    'sound' => 'default',
                    'data' => $data,
                    'badge' => $badge,
                );
            } else {
                //silent
                unset($data['silent_flag']);
                $body['aps'] = array(
                    'content-available' => 1,
                    'data' => $data,
                    'badge' => $badge,
                );
            }

            // Encode the payload as JSON
            $payload = json_encode($body);
            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $device_token) . pack('n', strlen($payload)) . $payload;
            // Send it to the server
            fwrite($fp, $msg, strlen($msg));
            fclose($fp);
        } catch (Exception $e) {
            return false;
        }
    }

}
