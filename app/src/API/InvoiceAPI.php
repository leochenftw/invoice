<?php

namespace App\Web\API;

use App\Web\Model\Invoice;
use Leochenftw\Restful\RestfulController;
use Leochenftw\Util;
use SilverStripe\Core\Convert;
use SilverStripe\Security\SecurityToken;

class InvoiceAPI extends RestfulController
{
    /**
     * Defines methods that can be called directly.
     *
     * @var array
     */
    private static $allowed_actions = [
        'get' => true,
        'post' => true,
    ];

    public function get($request)
    {
        return ['X-CSRF-TOKEN' => SecurityToken::inst()->getSecurityID()];
    }

    public function post($request)
    {
        if (Util::check_csrf($request)) {
            $action = $request->param('action') ?: 'save';
            $data = json_decode($request->postVar('data'));

            if ('save' == $action) {
                if (empty($data->client) || empty($data->client->id)) {
                    return $this->httpError(400, 'Missing client');
                }

                if (empty($data->logs) || empty($data->logs->list)) {
                    return $this->httpError(400, 'Nothing to bill');
                }
            } else {
                if (empty($data->id)) {
                    return $this->httpError(400, 'Missing invoice id');
                }
            }

            return $this->{$action}($data);
        }

        return $this->httpError(400, 'Missing CSRF token!');
    }

    private function paid(&$data)
    {
        if (empty($data->id)) {
            return $this->httpError(400, 'Missing invoice ID');
        }

        $invoice = Invoice::get()->byID($data->id);

        if (empty($invoice)) {
            return $this->httpError(404, 'Invoice not found');
        }

        $invoice->Paid = true;
        $invoice->write();

        return $invoice;
    }

    private function delete(&$data)
    {
        if (empty($data->id)) {
            return $this->httpError(400, 'Missing invoice ID');
        }

        $invoice = Invoice::get()->byID($data->id);

        if (empty($invoice)) {
            return $this->httpError(404, 'Invoice not found');
        }

        $invoice->delete();
    }

    private function save(&$data)
    {
        if (empty($data->id)) {
            $invoice = Invoice::create();
        } else {
            $invoice = Invoice::get()->byID($data->id);
        }

        $invoice->Title = Convert::raw2sql(strip_tags($data->title));
        $invoice->Content = strip_tags($data->content);
        $invoice->SideNote = strip_tags($data->sidenote);
        $invoice->InvoiceFooter = Convert::raw2sql($data->footer);
        $invoice->HourlyRate = Convert::raw2sql($data->hourly_rate);
        $invoice->EntityTitle = strip_tags($data->entity);
        $invoice->EntityAddress = strip_tags($data->entity_address);
        $invoice->TaxNumber = Convert::raw2sql($data->tax_no);
        $invoice->isGSTRegistered = Convert::raw2sql($data->gst_registered);
        $invoice->Paid = Convert::raw2sql($data->paid);
        $invoice->Due = Convert::raw2sql($data->due);
        $invoice->ClientID = Convert::raw2sql($data->client->id);
        $invoice->Logs = json_encode($data->logs->list);

        $invoice->write();

        return $invoice;
    }
}
