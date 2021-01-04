<?php

namespace App\Web\Model;

use SilverStripe\ORM\DataObject;

class Worklog extends DataObject implements \JsonSerializable
{
    private static $table_name = 'Worklog';

    private static $db = [
        'Start' => 'Datetime',
        'End' => 'Datetime',
        'Billed' => 'Boolean',
    ];

    private static $has_one = [
        'UserStory' => UserStory::class,
        'Project' => Project::class,
    ];

    private static $default_sort = [
        'Created' => 'DESC',
    ];

    public function ProjectTitle()
    {
        return $this->Project()->exists() ? $this->Project()->Title : '-';
    }

    public function LogTitle()
    {
        $project = $this->Project()->exists() ? $this->Project()->Title : '';

        return $project . ' - ' . ($this->UserStory()->exists() ? $this->UserStory()->Title : 'Standard dev work');
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if ($this->UserStory()->exists() && $this->UserStory()->Workflow()->exists()) {
            $this->ProjectID = $this->UserStory()->Workflow()->ProjectID;
        }
    }

    public function jsonSerialize()
    {
        return array_merge(
            $this->Data,
            [
                'title' => $this->UserStory()->exists() ? $this->UserStory()->Title : null,
            ]
        );
    }

    public function getHours()
    {
        $diff = strtotime($this->End) - strtotime($this->Start);

        return round(100 * $diff / 3600) * 0.01;
    }

    public function getData()
    {
        return [
            'id' => $this->ID,
            'start' => $this->Start,
            'end' => $this->End,
            'hours' => $this->Hours,
        ];
    }
}
