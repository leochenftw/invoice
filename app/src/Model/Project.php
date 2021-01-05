<?php

namespace App\Web\Model;

use SilverStripe\Assets\Image;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Parsers\URLSegmentFilter;

class Project extends DataObject implements \JsonSerializable
{
    private static $table_name = 'Project';

    private static $db = [
        'Title' => 'Varchar(128)',
        'Content' => 'Text',
        'URLSegment' => 'Varchar(1024)',
    ];

    private static $indexes = [
        'URLSegment' => true,
    ];

    private static $default_sort = ['Created' => 'DESC'];

    private static $has_one = [
        'Client' => Client::class,
        'Background' => Image::class,
    ];

    private static $has_many = [
        'Workflows' => Workflow::class,
        'Worklogs' => Worklog::class,
    ];

    private static $many_many = [
        'Users' => User::class,
    ];

    /**
     * CMS Fields.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->replaceField(
            'URLSegment',
            SiteTreeURLSegmentField::create('URLSegment', 'Slug')
                ->setURLPrefix(URLSegmentFilter::singleton()->filter($this->plural_name()) . '/')
        );

        return $fields;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->URLSegment = URLSegmentFilter::singleton()->filter($this->Title);
    }

    public function getData()
    {
        return [
            'id' => $this->ID,
            'title' => $this->Title,
            'client' => $this->Client()->exists() ? $this->Client()->Title : null,

            'slug' => $this->URLSegment,
            'background' => $this->Background()->exists() ? $this->Background()->Fit(356, 200)->URL : '//loremflickr.com/356/200',
        ];
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->ID,
            'title' => $this->Title,
            'client' => $this->Client()->exists() ? $this->Client()->Title : null,
            'slug' => $this->URLSegment,
            'background' => $this->Background()->exists() ? $this->Background()->Fit(1920, 1080)->URL : '//loremflickr.com/1920/1080',
            'workflows' => $this->Workflows()->Data,
            'pagetype' => 'Project',
        ];
    }
}
