<?php

namespace App\Web\API;

use App\Web\Controller\ProjectController;
use App\Web\Model\Client;
use App\Web\Model\Project;
use Leochenftw\Restful\RestfulController;
use Leochenftw\Util;
use SilverStripe\Core\Convert;
use SilverStripe\Security\SecurityToken;

class ProjectAPI extends RestfulController
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
            $desc = Convert::raw2sql($request->postVar('description'));
            $client_data = $request->postVar('client');

            $project = Project::create();
            $project->Title = $title;

            if (!empty($desc)) {
                $project->Content = $desc;
            }

            if (!empty($client_data)) {
                $client_data = json_decode($client_data);
                if (empty($client_data->id)) {
                    $client = Client::create();
                } else {
                    $client = Client::get()->byID(Convert::raw2sql($client_data->id));
                }

                $client->Title = Convert::raw2sql($client_data->title);
                $client->write();

                $project->ClientID = $client->ID;
            }

            $project->write();

            return ProjectController::create()->Data;
        }

        return $this->httpError(400, 'Missing CSRF token!');
    }
}
