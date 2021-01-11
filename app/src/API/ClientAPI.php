<?php

namespace App\Web\API;

use App\Web\Model\Client;
use Leochenftw\Restful\RestfulController;
use Leochenftw\Util;
use SilverStripe\Core\Convert;
use SilverStripe\Security\SecurityToken;

class ClientAPI extends RestfulController
{
    /**
     * Defines methods that can be called directly.
     *
     * @var array
     */
    private static $allowed_actions = [
        'get' => true,
        'post' => true,
        'delete' => true,
    ];

    public function delete($request)
    {
        if (Util::check_csrf($request)) {
            $id = Convert::raw2sql($request->param('id'));

            if (empty($id)) {
                return $this->httpError(400, 'Missing client id');
            }

            if ($client = Client::get()->byID($id)) {
                return $client->delete();
            }

            return $this->httpError(404, 'Client not found');
        }

        return $this->httpError(400, 'Missing CSRF token!');
    }

    public function get($request)
    {
        return ['X-CSRF-TOKEN' => SecurityToken::inst()->getSecurityID()];
    }

    public function post($request)
    {
        if (Util::check_csrf($request)) {
            $action = Convert::raw2sql($request->param('action'));

            if (empty($action)) {
                $action = 'createClient';
            }

            return $this->{$action}($request);
        }

        return $this->httpError(400, 'Missing CSRF token!');
    }

    private function updateClient(&$request)
    {
        if ($id = $request->param('id')) {
            if ($client = Client::get()->byID($id)) {
                return $this->createClient($request, $client);
            }

            return $this->httpError(404, 'No such client');
        }

        return $this->httpError(400, 'Missing id!');
    }

    private function createClient(&$request, $client = null)
    {
        $title = Convert::raw2sql($request->postVar('title'));

        if (empty($title)) {
            return $this->httpError(400, 'Missing client name');
        }

        $code = Convert::raw2sql($request->postVar('code'));

        if (empty($code)) {
            return $this->httpError(400, 'Missing client code');
        }

        $entity = Convert::raw2sql($request->postVar('entity'));
        $address = strip_tags($request->postVar('address'));
        $email = Convert::raw2sql($request->postVar('email'));
        $phone = Convert::raw2sql($request->postVar('phone'));

        $client = $client ?: Client::create();
        $client->Title = $title;
        $client->Code = $code;

        $client->Address = !empty($address) ? $address : null;
        $client->Email = !empty($email) ? $email : null;
        $client->BusinessEntity = !empty($entity) ? $entity : null;
        $client->Phone = !empty($phone) ? $phone : null;

        $client->write();

        return $client->Data;
    }
}
