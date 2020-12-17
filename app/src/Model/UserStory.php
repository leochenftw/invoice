<?php

namespace App\Web\Model;

use SilverStripe\ORM\DataObject;

class UserStory extends DataObject
{
    private static $table_name = 'UserStory';

    private static $db = [
        'Title' => 'Varchar(128)',
        'Content' => 'HTMLText',
    ];

    private static $has_one = [
        'Workflow' => Workflow::class,
        'CreatedBy' => User::class,
        'AssignedTo' => User::class,
    ];

    private static $has_many = [
        'Comments' => Comment::class,
    ];

    private static $many_many = [
        'Users' => User::class,
    ];
}
