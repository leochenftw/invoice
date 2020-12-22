<?php

namespace App\Web\API;

use App\Web\Model\UserStory;
use Leochenftw\Restful\RestfulController;
use Leochenftw\Util;
use SilverStripe\Core\Convert;
use SilverStripe\Security\SecurityToken;

class UserStoryAPI extends RestfulController
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
                return $this->httpError(400, 'User story cannot be empty');
            }

            $story = UserStory::create();
            $story->Title = $title;

            $story->write();

            return $story->Data;
        }

        return $this->httpError(400, 'Missing CSRF token!');
    }
}
