<?php

namespace App\Web\API;

use App\Web\Model\Worklog;
use Leochenftw\Restful\RestfulController;
use Leochenftw\Util;
use SilverStripe\Core\Convert;
use SilverStripe\Security\SecurityToken;

class WorklogAPI extends RestfulController
{
    private $log;
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
            $this->log = Worklog::get()->byID($id);
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
                    $this->log = Worklog::get()->byID($id);
                }

                if (empty($this->log)) {
                    return $this->httpError(400, 'not found Worklog');
                }

                return $this->{$action}();
            }
        }

        return $this->httpError(400, 'Missing CSRF token!');
    }

    private function updateHours()
    {
        if (!$this->request->isPOST()) {
            return $this->httpError(400, 'Bad method');
        }

        $start = Convert::raw2sql($this->request->postVar('start'));
        $end = Convert::raw2sql($this->request->postVar('end'));

        if (empty($start) || empty($end)) {
            return $this->httpError(400, 'Missing start time or end time');
        }

        if (strtotime($end) <= strtotime($start)) {
            return $this->httpError(400, 'End time must be greater than start time');
        }

        $this->log->Start = $start;
        $this->log->End = $end;
        $this->log->write();

        return $this->log;
    }
}
