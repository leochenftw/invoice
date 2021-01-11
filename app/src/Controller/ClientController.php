<?php

namespace App\Web\Controller;

use App\Web\Model\Client;
use Page;
use PageController;
use SilverStripe\Core\Convert;

class ClientController extends PageController
{
    protected $Title = 'Clients';

    public function getData()
    {
        $slug = Convert::raw2sql($this->request->param('slug'));
        $action = Convert::raw2sql($this->request->param('action'));
        $data = Page::create()->Data;

        if (!empty($slug)) {
            if ($client = Client::get()->filter(['URLSegment' => $slug])->first()) {
                $data['title'] = $client->Title;
                $data['pagetype'] = 'Client';

                if (!empty($action) && $this->hasMethod($action)) {
                    return $this->{$action}($client);
                }

                return array_merge($data, $client->jsonSerialize(true));
            }
        }

        $page = Convert::raw2sql($this->request->getVar('page'));
        $page = empty($page) ? 0 : $page;

        $data['title'] = $this->Title;
        $data['pagetype'] = 'ClientList';

        return array_merge($data, [
            'list' => [
                'headers' => [
                    [
                        'text' => 'Client',
                        'align' => 'start',
                        'sortable' => true,
                        'value' => 'title',
                    ],
                    [
                        'text' => 'Code',
                        'align' => 'center',
                        'sortable' => true,
                        'value' => 'code',
                    ],
                    [
                        'text' => 'Projects',
                        'align' => 'center',
                        'sortable' => false,
                        'value' => 'projects',
                    ],
                    [
                        'text' => 'Billable Hours',
                        'align' => 'center',
                        'sortable' => true,
                        'value' => 'billable',
                    ],
                    [
                        'text' => 'Outstanding Invoice(s)',
                        'align' => 'center',
                        'sortable' => false,
                        'value' => 'outstanding_invoices',
                    ],
                    [
                        'text' => '',
                        'align' => 'center',
                        'sortable' => false,
                        'value' => 'actions',
                    ],
                ],
                'clients' => array_map(function ($item) {
                    return $item->TableData;
                }, Client::get()->toArray()),
            ],
        ]);
    }

    public function activities(&$client)
    {
        return $client->Activities;
    }
}
