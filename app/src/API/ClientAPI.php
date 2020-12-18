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
    ];

    public function get($request)
    {
        return ['X-CSRF-TOKEN' => SecurityToken::inst()->getSecurityID()];
    }

    public function post($request)
    {
        if (Util::check_csrf($request)) {
            $title = Convert::raw2sql($request->postVar('title'));

            if (empty($title)) {
                return $this->httpError(400, 'Missing client name');
            }

            $code = Convert::raw2sql($request->postVar('code'));
            $address = Convert::raw2sql($request->postVar('address'));
            $email = Convert::raw2sql($request->postVar('email'));
            $phone = Convert::raw2sql($request->postVar('phone'));

            $client = Client::create();
            $client->Title = $title;

            if (!empty($code)) {
                $client->Code = $code;
            }

            if (!empty($address)) {
                $client->Address = $address;
            }

            if (!empty($email)) {
                $client->Email = $email;
            }

            if (!empty($phone)) {
                $client->Phone = $phone;
            }

            $client->write();

            return $client->Data;
        }

        return $this->httpError(400, 'Missing CSRF token!');
    }
}
