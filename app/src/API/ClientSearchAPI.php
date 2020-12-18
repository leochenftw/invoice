<?php

namespace App\Web\API;

use App\Web\Model\Client;
use Leochenftw\Restful\RestfulController;
use SilverStripe\Core\Convert;

class ClientSearchAPI extends RestfulController
{
    /**
     * Defines methods that can be called directly.
     *
     * @var array
     */
    private static $allowed_actions = [
        'get' => true,
    ];

    public function get($request)
    {
        $title = Convert::raw2sql(trim($request->param('search')));

        if (empty($title)) {
            return [];
        }

        return Client::get()->filterAny(['Title:StartsWith:nocase' => $title, 'Code:StartsWith:nocase' => $title])->limit(5)->Data;
    }
}
