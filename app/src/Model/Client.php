<?php

namespace App\Web\Model;

use SilverStripe\ORM\DataObject;

class Client extends DataObject implements \JsonSerializable
{
    private static $table_name = 'Client';

    private static $db = [
        'Title' => 'Varchar(128)',
        'Code' => 'Varchar(16)',
        'Address' => 'Text',
        'Email' => 'Varchar(255)',
        'Phone' => 'Varchar(32)',
    ];

    private static $has_one = [
        'PrimaryContact' => User::class,
    ];

    private static $has_many = [
        'Projects' => Project::class,
    ];

    public function jsonSerialize()
    {
        return array_merge(
            $this->Data,
            [
                'address' => $this->Address,
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
            'code' => $this->Code,
        ];
    }
}
