<?php

namespace App\Web\API;

use App\Web\Model\UserStory;
use App\Web\Model\Worklog;
use Leochenftw\Restful\RestfulController;
use Leochenftw\Util;
use SilverStripe\Core\Convert;
use SilverStripe\Security\SecurityToken;

class UserStoryAPI extends RestfulController
{
    private $story;
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
        if ($id = Convert::raw2sql($request->param('id'))) {
            $this->story = UserStory::get()->byID($id);
        }

        $action = $request->param('action');

        if (!$action) {
            return ['X-CSRF-TOKEN' => SecurityToken::inst()->getSecurityID()];
        }

        return $this->{$action}();
    }

    public function post($request)
    {
        if (Util::check_csrf($request)) {
            $action = $request->param('action');

            if ($action) {
                if ($id = Convert::raw2sql($request->param('id'))) {
                    $this->story = UserStory::get()->byID($id);
                }

                if (empty($this->story)) {
                    return $this->httpError(400, 'not found userstory');
                }

                return $this->{$action}();
            }

            $title = Convert::raw2sql($request->postVar('title'));

            if (empty($title)) {
                return $this->httpError(400, 'User story cannot be empty');
            }

            $story = UserStory::create();
            $story->Title = $title;

            $story->write();

            return $story->Data;
        }

        return $this->httpError(400, 'Missing CSRF token!');
    }

    private function getFullstory()
    {
        return $this->story;
    }

    private function update()
    {
        $title = Convert::raw2sql($this->request->postVar('title'));
        $description = Convert::raw2sql($this->request->postVar('content'));
        $hours_allocated = Convert::raw2sql($this->request->postVar('hours_allocated'));

        if (empty($title)) {
            return $this->httpError(400, 'User story cannot be empty');
        }

        $this->story->Title = $title;
        $this->story->Content = $description;
        $this->story->HoursAllocated = $hours_allocated;
        $this->story->write();

        return $this->story;
    }

    private function addHours()
    {
        $hours = Convert::raw2sql($this->request->postVar('hours'));

        if (empty($hours)) {
            return $this->httpError(400, 'User story cannot be empty');
        }

        $seconds = $hours * 3600;

        $log = Worklog::create();
        $now = new \DateTime();
        $log->End = $now->format('Y-m-d H:i') . ':00';
        $log->Start = $now->modify("-{$seconds} seconds")->format('Y-m-d H:i') . ':00';
        $log->UserStoryID = $this->story->ID;
        $log->write();

        return $this->story;
    }
}
