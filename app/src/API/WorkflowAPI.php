<?php

namespace App\Web\API;

use App\Web\Model\UserStory;
use App\Web\Model\Workflow;
use Leochenftw\Restful\RestfulController;
use Leochenftw\Util;
use SilverStripe\Core\Convert;
use SilverStripe\Security\SecurityToken;

class WorkflowAPI extends RestfulController
{
    private $workflow;

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
            $id = Convert::raw2sql($request->param('id'));
            $action = Convert::raw2sql($request->param('action'));

            if (!empty($id)) {
                $this->workflow = Workflow::get()->byID($id);
            }

            if (!$this->hasMethod($action)) {
                return $this->httpError(400, 'Action not allowed');
            }

            return $this->{$action}();
        }

        return $this->httpError(400, 'Missing CSRF token!');
    }

    private function rearrange()
    {
        if (empty($this->workflow)) {
            return $this->httpError(404, 'No such workflow');
        }

        $list = json_decode($this->request->postVar('list'));

        $n = 0;
        foreach ($list as $item) {
            $story = UserStory::get()->byID($item->id);
            $story->Sort = $n;
            $story->write();
            $this->workflow->UserStories()->add($item->id);
            ++$n;
        }
    }

    private function addUserStory()
    {
        if (empty($this->workflow)) {
            return $this->httpError(404, 'No such workflow');
        }

        $title = Convert::raw2sql($this->request->postVar('title'));

        $story = UserStory::create();
        $story->WorkflowID = $this->workflow->ID;
        $story->Title = $title;
        $story->Sort = $this->workflow->UserStories()->count();

        $story->write();

        return $story->Data;
    }
}
