<?php

namespace App\Web\Model;

use Leochenftw\Util;
use SilverStripe\ORM\DataObject;

class UserStory extends DataObject implements \JsonSerializable
{
    private static $table_name = 'UserStory';

    private static $db = [
        'Title' => 'Varchar(128)',
        'Content' => 'HTMLText',
        'Sort' => 'Int',
    ];

    private static $has_one = [
        'Workflow' => Workflow::class,
        'CreatedBy' => User::class,
        'AssignedTo' => User::class,
    ];

    private static $has_many = [
        'Worklogs' => Worklog::class,
        'Comments' => Comment::class,
    ];

    private static $many_many = [
        'Users' => User::class,
    ];

    public function jsonSerialize()
    {
        return array_merge(
            $this->Data,
            [
                'content' => Util::preprocess_content($this->Content),
            ]
        );
    }

    public function getData()
    {
        return [
            'id' => $this->ID,
            'title' => $this->Title,
        ];
    }
}
