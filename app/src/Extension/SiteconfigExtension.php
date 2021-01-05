<?php

namespace App\Web\Extension;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HtmlEditorField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

class SiteconfigExtension extends DataExtension
{
    private static $db = [
        'HourlyRate' => 'Decimal',
        'GSTRate' => 'Decimal',
        'EntityTitle' => 'Varchar(128)',
        'EntityAddress' => 'Text',
        'TaxNumber' => 'Varchar(128)',
        'isGSTRegistered' => 'Boolean',
        'InvoiceFooter' => 'HTMLText',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.WorkSettings',
            [
                TextField::create('GSTRate', 'GST rate')->setDescription('e.g. 0.15'),
                CurrencyField::create(
                    'HourlyRate',
                    'Default hourly rate'
                ),
                TextField::create('EntityTitle', 'Your entity name')
                    ->setDescription('e.g. John Doe if you are an individual, or Welly Digital if you are a business.'),
                TextareaField::create('EntityAddress', 'Entity Address')
                    ->setDescription('The address where your service is delivered.'),
                TextField::create('TaxNumber', 'IRD/GST No.'),
                CheckboxField::create(
                    'isGSTRegistered',
                    'The entity is GST registered'
                ),
                HtmlEditorField::create(
                    'InvoiceFooter',
                    'Invoice footer'
                ),
            ]
        );
    }
}
