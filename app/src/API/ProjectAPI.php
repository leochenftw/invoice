<?php

namespace App\Web\API;

use Leochenftw\Restful\RestfulController;
use Leochenftw\Util;
use SilverStripe\Security\SecurityToken;
use App\Web\Model\Project;
use App\Web\Controller\ProjectController;
use SilverStripe\Core\Convert;

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
            // $code = Convert::raw2sql($request->postVar('code'));
            $desc = Convert::raw2sql($request->postVar('description'));

            $project = Project::create();
            $project->Title = $title;
            // $project->Code = $code;
            $project->Content = $desc;
            $project->write();

            return ProjectController::create()->Data;
        }

        return $this->httpError(400, 'Missing CSRF token!');
    }
}
