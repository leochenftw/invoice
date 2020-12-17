<?php

namespace App\Web\Model;

use SilverStripe\ORM\DataObject;

class Comment extends DataObject
{
    private static $table_name = 'Comment';

    private static $db = [
        'Content' => 'HTMLText',
    ];

    private static $has_one = [
        'UserStory' => UserStory::class,
    ];
}
