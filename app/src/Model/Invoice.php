<?php

namespace App\Web\Model;

use Leochenftw\Util;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\GroupedList;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;

class Invoice extends DataObject implements \JsonSerializable
{
    private static $table_name = 'Invoice';

    private static $db = [
        'Title' => 'Varchar(128)',
        'Content' => 'Text',
        'SideNote' => 'Text',
        'InvoiceFooter' => 'HTMLText',
        'HourlyRate' => 'Decimal',
        'EntityTitle' => 'Varchar(128)',
        'EntityAddress' => 'Text',
        'TaxNumber' => 'Varchar(128)',
        'isGSTRegistered' => 'Boolean',
        'Paid' => 'Boolean',
        'Due' => 'Date',
        'Logs' => 'Text',
    ];

    private static $default_sort = [
        'Due' => 'ASC',
        'Created' => 'DESC',
    ];

    private static $indexes = [
        'Title' => 'unique',
    ];

    private static $has_one = [
        'Client' => Client::class,
    ];

    private static $many_many = [
        'LinkedLogs' => Worklog::class,
    ];

    /**
     * Event handler called before deleting from the database.
     *
     * @uses DataExtension->onBeforeDelete()
     */
    public function onBeforeDelete()
    {
        parent::onBeforeDelete();
        foreach ($this->LinkedLogs() as $log) {
            $log->Billed = false;
            $log->write();
        }
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        foreach ($this->LinkedLogs() as $linkedlogs) {
            $linkedlogs->Billed = false;
            $linkedlogs->write();
        }

        $this->LinkedLogs()->removeAll();

        $list = json_decode($this->Logs);

        foreach ($list as $item) {
            foreach ($item->ids as $id) {
                $log = Worklog::get()->byID($id);
                $log->Billed = true;
                $log->write();
                $this->LinkedLogs()->add($id);
            }
        }
    }

    public function populateDefaults()
    {
        parent::populateDefaults();
        $n = Invoice::get()->filter(['Created:GreaterThanOrEqual' => date('Y-m-d 00:00:00', time()), 'Created:LessThanOrEqual' => date('Y-m-d 23:59:59', time())])->count() + 1;
        $this->Due = $this->getNextDueDay();
        $this->Title = date('Ymd', time()) . str_pad($n, 3, '0', STR_PAD_LEFT);
        $siteconfig = SiteConfig::current_site_config();
        $this->HourlyRate = $siteconfig->HourlyRate;
        $this->EntityTitle = $siteconfig->EntityTitle;
        $this->EntityAddress = $siteconfig->EntityAddress;
        $this->TaxNumber = $siteconfig->TaxNumber;
        $this->isGSTRegistered = $siteconfig->isGSTRegistered;
        $this->InvoiceFooter = $siteconfig->InvoiceFooter;
    }

    public function jsonSerialize()
    {
        return array_merge(
            $this->Data,
            [
                'gst_rate' => SiteConfig::current_site_config()->GSTRate,
                'content' => !empty($this->Content) ? nl2br($this->Content) : $this->InvolvedProjects,
                'sidenote' => !empty($this->SideNote) ? nl2br($this->SideNote) : 'Website work',
                'footer' => $this->FooterContent,
                'entity' => $this->EntityTitle,
                'entity_address' => nl2br($this->EntityAddress),
                'tax_no' => $this->TaxNumber,
                'gst_registered' => $this->isGSTRegistered,
                'logs' => $this->OutstandingLogs,
                'client' => $this->Client()->exists() ? $this->Client() : null,
            ]
        );
    }

    public function getFooterContent()
    {
        // shitupid SilverStripe sanitiser...
        return str_replace('=""', ';"', str_replace(':" ', ':', str_replace('"\&quot;', '"', Util::preprocess_content($this->InvoiceFooter))));
    }

    public function getStoredLogs()
    {
        if (!empty($this->Logs)) {
            $list = json_decode($this->Logs);

            foreach ($list as &$item) {
                $item = ArrayData::create($item);
            }

            return ArrayList::create($list);
        }
    }

    public function numberFormatter($n)
    {
        return number_format($n, 2);
    }

    public function getSubtotal()
    {
        if (!empty($this->Logs)) {
            $list = json_decode($this->Logs);

            return '$' . $this->numberFormatter(array_sum(array_map(function ($log) {
                return $log->sum;
            }, $list)));
        }

        return '$0.00';
    }

    public function getGrandtotal()
    {
        if (!empty($this->Logs)) {
            $list = json_decode($this->Logs);

            $n = array_sum(array_map(function ($log) {
                return $log->sum;
            }, $list));

            if ($this->isGSTRegistered) {
                $n = $n * 1.15;
            }

            return '$' . $this->numberFormatter($n);
        }

        return '$0.00';
    }

    public function getOutstandingLogs()
    {
        $headers = [
            [
                'text' => 'Description',
                'align' => 'start',
                'sortable' => false,
                'value' => 'title',
            ],
            [
                'text' => 'Hours',
                'align' => 'end',
                'sortable' => false,
                'value' => 'hours',
            ],
            [
                'text' => 'Hourly Rate',
                'align' => 'end',
                'sortable' => false,
                'value' => 'hourly_rate',
            ],
            [
                'text' => 'Total price',
                'align' => 'end',
                'sortable' => false,
                'value' => 'sum',
            ],
        ];

        if (!empty($this->Logs)) {
            $logs = json_decode($this->Logs);

            return [
                'headers' => $headers,
                'list' => $logs,
            ];
        }

        if ($this->Client()->exists()) {
            $log_ids = $this->Client()->OutstandingWorklogs;
            if (!empty($log_ids)) {
                $groups = GroupedList::create(Worklog::get()->filter(['ID' => $log_ids]))->GroupedBy('LogTitle');

                $list = [];
                foreach ($groups as $group) {
                    $list[$group->LogTitle] = [
                        'ids' => array_map(function ($log) {
                            return $log->ID;
                        }, $group->Children->toArray()),
                        'hours' => array_sum(array_map(function ($log) {
                            return $log->Hours;
                        }, $group->Children->toArray())),
                    ];
                }

                $logs = [];

                $n = 0;
                foreach ($list as $key => $value) {
                    $logs[] = [
                        'index' => $n,
                        'title' => $key,
                        'ids' => $value['ids'],
                        'hours' => $value['hours'],
                        'hourly_rate' => $this->HourlyRate,
                        'sum' => $value['hours'] * $this->HourlyRate,
                    ];
                    ++$n;
                }

                return [
                    'headers' => $headers,
                    'list' => $logs,
                ];
            }
        }

        return [
            'headers' => $headers,
            'list' => [],
        ];
    }

    public function getInvolvedProjects()
    {
        if ($this->Client()->exists()) {
            $log_ids = $this->Client()->OutstandingWorklogs;
            if (!empty($log_ids)) {
                $logs = GroupedList::create(Worklog::get()->filter(['ID' => $log_ids]))->GroupedBy('ProjectTitle')->column('ProjectTitle');

                if (count($logs) > 1) {
                    $last = array_pop($xlogs);

                    return implode(', ', $logs) . ' and ' . $last;
                }

                return implode(', ', $logs);
            }
        }

        return '-';
    }

    public function getData()
    {
        return [
            'id' => $this->ID,
            'title' => $this->Title,
            'client' => $this->Client()->exists() ? $this->Client()->Title : '-',
            'hourly_rate' => $this->HourlyRate,
            'overdue' => $this->Paid ? true : $this->Due < time(),
            'paid' => $this->Paid,
            'due' => $this->Due,
            'hours' => $this->Hours,
        ];
    }

    public function getHours()
    {
        if ($this->exists()) {
            $list = json_decode($this->Logs);

            return array_sum(array_map(function ($log) {
                return $log->hours;
            }, $list));
        }

        return '-';
    }

    private function getNextDueDay()
    {
        $due = date('Y-m', time()) . '-20';

        if (strtotime($due) < time()) {
            $due = date('Y-m', strtotime('+1 month')) . '-20';
        }

        return $due;
    }
}
