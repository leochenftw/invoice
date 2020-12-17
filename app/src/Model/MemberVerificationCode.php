<?php

namespace App\Web\Model;

use SilverStripe\ORM\DataObject;
use Ramsey\Uuid\Uuid;

class MemberVerificationCode extends DataObject
{
    private static $table_name = 'MemberVerificationCode';

    private static $db = [
        'Code' => 'Varchar(40)',
        'Invalid' => 'Boolean',
    ];

    private static $has_one = [
        'User' => User::class,
    ];

    private static $indexes = [
        'Code' => [
            'type' => 'unique',
        ],
    ];

    public static function createOnePassCode(User $user, $type = 'activation')
    {
        if ($type == 'activation') {
            $bytes = random_bytes(32);
            $code = strtolower(substr(bin2hex($bytes), 0, 6));
        } else {
            $uuid = Uuid::uuid4();
            $code = $uuid->toString();
        }

        $verificationCode = new self();
        $verificationCode->Code = $code;
        $verificationCode->Invalid = false;
        $verificationCode->UserID = $user->ID;
        $verificationCode->write();

        return $verificationCode;
    }
}
