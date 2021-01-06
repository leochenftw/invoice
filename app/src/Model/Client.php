<?php

namespace App\Web\Model;

use SilverStripe\Dev\Debug;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Parsers\URLSegmentFilter;

class Client extends DataObject implements \JsonSerializable
{
    private static $table_name = 'Client';

    private static $db = [
        'Title' => 'Varchar(128)',
        'Code' => 'Varchar(16)',
        'BusinessEntity' => 'Varchar(64)',
        'Address' => 'Text',
        'Email' => 'Varchar(255)',
        'Phone' => 'Varchar(32)',
        'URLSegment' => 'Varchar(1024)',
    ];

    private static $indexes = [
        'URLSegment' => true,
    ];

    private static $default_sort = ['Title' => 'ASC'];

    private static $has_one = [
        'PrimaryContact' => User::class,
    ];

    private static $has_many = [
        'Invoices' => Invoice::class,
        'Projects' => Project::class,
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

    public function jsonSerialize()
    {
        return array_merge(
            $this->Data,
            [
                'entity' => !empty($this->BusinessEntity) ? $this->BusinessEntity : $this->Title,
                'address' => nl2br($this->Address),
                'email' => $this->Email,
                'phone' => $this->Phone,
            ]
        );
    }

    public function getData()
    {
        return [
            'id' => $this->ID,
            'title' => $this->Title,
            'code' => $this->Code ?: '-',
            'slug' => $this->URLSegment ?: $this->makeSlug(),
        ];
    }

    public function getOutstandingWorklogs()
    {
        $list = array_map(function ($project) {
            return array_map(function ($log) {
                return $log->ID;
            }, $project->Worklogs()->filter(['Billed' => false])->toArray());
        }, $this->Projects()->toArray());

        if (empty($list)) {
          return $list;
        }

        return array_merge(...array_values($list));
    }

    public function getTableData()
    {
        return array_merge(
            $this->Data,
            [
                'projects' => $this->Projects()->count(),
                'billable' => array_sum(array_map(function ($project) {
                    return array_sum(array_map(function ($log) {
                        return $log->Hours;
                    }, $project->Worklogs()->filter(['Billed' => false])->toArray()));
                }, $this->Projects()->toArray())),
                'outstanding_invoices' => $this->Invoices()->filter(['Paid' => false])->count(),
            ]
        );
    }

    private function makeSlug()
    {
        $this->write();

        return $this->URLSegment;
    }
}
