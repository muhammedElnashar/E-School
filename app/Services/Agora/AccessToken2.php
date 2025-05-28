<?php

namespace App\Services\Agora;

class AccessToken2
{
    const VERSION = '007';
    const PRIVILEGE_JOIN_CHANNEL = 1;
    const PRIVILEGE_PUBLISH_AUDIO_STREAM = 2;
    const PRIVILEGE_PUBLISH_VIDEO_STREAM = 3;
    const PRIVILEGE_PUBLISH_DATA_STREAM = 4;

    private $appId;
    private $appCertificate;
    private $channelName;
    private $uid;
    private $salt;
    private $ts;
    private $messages = [];

    public function __construct($appId, $appCertificate, $channelName, $uid)
    {
        $this->appId = $appId;
        $this->appCertificate = $appCertificate;
        $this->channelName = $channelName;
        $this->uid = $uid;
        $this->salt = random_int(1, 99999999);
        $this->ts = time() + 24 * 3600;
    }

    public function addPrivilege($privilege, $expireTimestamp)
    {
        $this->messages[$privilege] = $expireTimestamp;
    }

    public function build()
    {
        $pack = $this->packMessages();
        $sig = hash_hmac('sha256', $this->appId . $this->channelName . $this->uid . $pack, $this->appCertificate, true);

        $content = pack('N', strlen($sig)) . $sig . $pack;
        $base64 = base64_encode($content);

        return self::VERSION . $this->appId . $base64;
    }

    private function packMessages()
    {
        $data = '';
        $data .= pack('N', $this->salt);
        $data .= pack('N', $this->ts);
        $data .= pack('n', count($this->messages));
        foreach ($this->messages as $k => $v) {
            $data .= pack('nN', $k, $v);
        }
        return $data;
    }
}
