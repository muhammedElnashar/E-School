<?php

namespace App\Services\Agora;

class RtcTokenBuilder2
{
    const ROLE_PUBLISHER = 1;
    const ROLE_SUBSCRIBER = 2;

    public static function buildTokenWithUid($appId, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs)
    {
        $token = new AccessToken2($appId, $appCertificate, $channelName, $uid);
        $token->addPrivilege(AccessToken2::PRIVILEGE_JOIN_CHANNEL, $privilegeExpiredTs);

        if ($role == self::ROLE_PUBLISHER) {
            $token->addPrivilege(AccessToken2::PRIVILEGE_PUBLISH_AUDIO_STREAM, $privilegeExpiredTs);
            $token->addPrivilege(AccessToken2::PRIVILEGE_PUBLISH_VIDEO_STREAM, $privilegeExpiredTs);
            $token->addPrivilege(AccessToken2::PRIVILEGE_PUBLISH_DATA_STREAM, $privilegeExpiredTs);
        }

        return $token->build();
    }
}
