<?php

namespace App\Web\Model;

use SilverStripe\ORM\DataObject;

class Workflow extends DataObject
{
    private static $table_name = 'Workflow';

    private static $db = [
        'Title' => 'Varchar(128)',
        'Sort' => 'Int',
    ];

    private static $has_one = [
        'Project' => Project::class,
    ];

    private static $has_many = [
        'UserStories' => UserStory::class,
    ];
}
