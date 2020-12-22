<?php

namespace App\Web\Model;

use SilverStripe\ORM\DataObject;

class Worklog extends DataObject implements \JsonSerializable
{
    private static $table_name = 'Worklog';

    private static $db = [
        'Start' => 'Datetime',
        'End' => 'Datetime',
    ];

    private static $has_one = [
        'UserStory' => UserStory::class,
    ];

    public function jsonSerialize()
    {
        $diff = (strtotime($this->End) - strtotime($this->Start)) / 3600;
        $diff = round($diff * 100) * 0.01;

        return array_merge(
            $this->Data,
            [
                'duration_raw' => strtotime($this->End) - strtotime($this->Start),
                'duration' => $diff,
            ]
        );
    }

    public function getData()
    {
        return [
            'id' => $this->ID,
            'start' => strtotime($this->Start),
            'end' => strtotime($this->End),
        ];
    }
}
