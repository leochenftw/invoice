<?php

namespace App\Web\Controller;

use App\Web\Model\Invoice;
use Mpdf\Mpdf;
use Page;
use PageController;
use SilverStripe\Core\Convert;
use SilverStripe\View\Requirements;

class InvoiceController extends PageController
{
    protected $Title = 'Invoice';

    private static $allowed_actions = [
        'export',
    ];

    public function index($request)
    {
        if ('export' == $request->param('action')) {
            return $this->export($request);
        }

        return $this->renderWith('Page');
    }

    public function export($request)
    {
        $id = $request->param('id');

        if (!empty($id) && is_numeric($id)) {
            if ($invoice = Invoice::get()->byID($id)) {
                $str = $this->customise($invoice)->renderWith(Invoice::class);

                $mpdf = new Mpdf([
                    'mode' => 'utf-8',
                    'CSSselectMedia' => 'screen',
                    'disablePrintCSS' => true,
                ]);

                // return $str;
                // Write some HTML code:
                $mpdf->WriteHTML($str);

                return $mpdf->Output();
            }
        }

        return $this->httpError(404);
    }

    public function getData()
    {
        $id = Convert::raw2sql($this->request->param('id'));
        $data = Page::create()->Data;

        if (!empty($id) && is_numeric($id)) {
            if ($invoice = Invoice::get()->byID($id)) {
                return array_merge($data, $invoice->jsonSerialize(), [
                    'pagetype' => 'Invoice',
                ]);
            }
        }
        if ('new' == $id) {
            $client_id = Convert::raw2sql($this->request->getVar('client') ?: 0);
            $invoice = Invoice::create(['ClientID' => $client_id])->jsonSerialize();

            return array_merge($data, $invoice, [
                'pagetype' => 'Invoice',
            ]);
        }

        $page = Convert::raw2sql($this->request->getVar('page'));
        $page = empty($page) ? 0 : $page;

        $data['title'] = $this->Title;
        $data['pagetype'] = 'InvoiceList';

        return array_merge($data, [
            'list' => [
                'headers' => [
                    [
                        'text' => 'Invoice No.',
                        'align' => 'start',
                        'sortable' => true,
                        'value' => 'title',
                    ],
                    [
                        'text' => 'Client',
                        'align' => 'center',
                        'sortable' => true,
                        'value' => 'client',
                    ],
                    [
                        'text' => 'Hourly Rate',
                        'align' => 'center',
                        'sortable' => false,
                        'value' => 'hourly_rate',
                    ],
                    [
                        'text' => 'Hours',
                        'align' => 'center',
                        'sortable' => true,
                        'value' => 'hours',
                    ],
                    [
                        'text' => 'Due',
                        'align' => 'center',
                        'sortable' => true,
                        'value' => 'due',
                    ],
                    [
                        'text' => 'Paid',
                        'align' => 'center',
                        'sortable' => false,
                        'value' => 'paid',
                    ],
                    [
                        'text' => '',
                        'align' => 'center',
                        'sortable' => false,
                        'value' => 'actions',
                    ],
                ],
                'clients' => array_map(function ($item) {
                    return $item->Data;
                }, Invoice::get()->toArray()),
            ],
        ]);
    }

    protected function init()
    {
        parent::init();

        if ('export' == $this->request->param('action')) {
            Requirements::css('leochenftw/leoss4bk: client/dist/pdf.css');
            Requirements::block('leochenftw/leoss4bk: client/dist/vendor.js');
            Requirements::block('leochenftw/leoss4bk: client/dist/app.js');
        }
    }
}
