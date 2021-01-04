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
        'HoursAllocated' => 'Decimal',
        'Sort' => 'Int',
    ];

    private static $default_sort = [
        'Sort' => 'ASC',
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

    public function getHoursSpent()
    {
        if ($this->Worklogs()->exists()) {
            return array_sum(array_map(function ($log) {
                return $log->Hours;
            }, $this->Worklogs()->toArray()));
        }

        return 0;
    }

    public function jsonSerialize()
    {
        return array_merge(
            $this->Data,
            [
                'content' => Util::preprocess_content($this->Content),
                'hours_spent' => $this->HoursSpent,
                'logs' => array_map(function ($log) {
                    return $log;
                }, $this->Worklogs()->toArray()),
            ]
        );
    }

    public function getData()
    {
        return [
            'id' => $this->ID,
            'title' => $this->Title,
            'hours_allocated' => $this->HoursAllocated,
        ];
    }
}
