<?php

namespace App\Web\Model;

use SilverStripe\ORM\DataObject;


class Client extends DataObject
{
    private static $table_name = 'Client';

    private static $db = [
        'Title' => 'Varchar(128)',
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
}
