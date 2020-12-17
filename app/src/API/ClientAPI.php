<?php

namespace App\Web\API;

use SilverStripe\Core\Convert;
use Leochenftw\Restful\RestfulController;
use Leochenftw\Util;
use SilverStripe\Security\SecurityToken;
use App\Web\Model\Client;

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

            $client = Client::create();
            $client->Title = $title;
        }

        return $this->httpError(400, 'Missing CSRF token!');
    }
}
