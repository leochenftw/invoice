<?php

namespace App\Web\Model;

use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Parsers\URLSegmentFilter;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;

class Project extends DataObject
{
    private static $table_name = 'Project';

    private static $db = [
        'Title' => 'Varchar(128)',
        'Content' => 'Text',
        'URLSegment' => 'Varchar(1024)',
    ];

    private static $default_sort = ['Created' => 'DESC'];

    private static $has_one = [
        'Client' => Client::class,
        'Background' => Image::class,
    ];

    private static $has_many = [
        'Workflows' => Workflow::class,
    ];

    private static $many_many = [
        'Users' => User::class,
    ];

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->replaceField(
            'URLSegment',
            SiteTreeURLSegmentField::create('URLSegment', 'Slug')
                ->setURLPrefix(URLSegmentFilter::singleton()->filter($this->plural_name())  . '/')
        );
        return $fields;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->URLSegment = URLSegmentFilter::singleton()->filter($this->Title);
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        if ($this->ProjectOwner()->exists() && !$this->Users()->byID($this->ProjectOwnerID)) {
            $this->Users()->add($this->ProjectOwner());
        }
    }

    public function getData()
    {
        return [
            'id' => $this->ID,
            'title' => $this->Title,
            'client' => $this->Client()->exists() ? $this->Client()->Title : null,
            'slug' => $this->URLSegment,
        ];
    }
}
