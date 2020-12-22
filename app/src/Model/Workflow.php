<?php

namespace App\Web\Model;

use SilverStripe\ORM\DataObject;

class Workflow extends DataObject implements \JsonSerializable
{
    private static $table_name = 'Workflow';

    private static $db = [
        'Title' => 'Varchar(128)',
        'Sort' => 'Int',
    ];

    private static $has_one = [
        'Project' => Project::class,
    ];

    private static $default_sort = [
        'Sort' => 'ASC',
    ];

    private static $has_many = [
        'UserStories' => UserStory::class,
    ];

    public function jsonSerialize()
    {
        return $this->Data;
    }

    public function getData()
    {
        return [
            'id' => $this->ID,
            'title' => $this->Title,
            'sort' => $this->Sort,
            'stories' => $this->UserStories()->Data,
        ];
    }
}
