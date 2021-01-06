<?php

namespace App\Web\API;

use App\Web\Controller\ProjectController;
use App\Web\Model\Client;
use App\Web\Model\Project;
use App\Web\Model\Workflow;
use Leochenftw\Restful\RestfulController;
use Leochenftw\Util;
use SilverStripe\AssetAdmin\Controller\AssetAdmin;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Convert;
use SilverStripe\Security\SecurityToken;

class ProjectAPI extends RestfulController
{
    private $project;
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
            if (!empty($id)) {
                if ($this->project = Project::get()->byID($id)) {
                    return $this->project->delete();
                }

                return $this->httpError(404, 'The project does not exist!');
            }
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
            $id = Convert::raw2sql($request->param('id'));
            $action = Convert::raw2sql($request->param('action'));

            if (!empty($id)) {
                $this->project = Project::get()->byID($id);
            } else {
                $action = 'createProject';
            }

            if ($this->hasMethod($action)) {
                return $this->{$action}();
            }
        }

        return $this->httpError(400, 'Missing CSRF token!');
    }

    private function createWorkflow()
    {
        if (empty($this->project)) {
            return $this->httpError(404, 'No such project');
        }

        $request = $this->request;

        $title = Convert::raw2sql($request->postVar('title'));

        $workflow = Workflow::create();
        $workflow->Title = $title;
        $workflow->ProjectID = $this->project->ID;

        $sort = Convert::raw2sql($request->postVar('sort')) ?: $this->project->Workflows()->count();
        $workflow->Sort = $sort;
        $workflow->write();

        return $this->project->jsonSerialize();
    }

    private function createProject()
    {
        $request = $this->request;
        $title = Convert::raw2sql($request->postVar('title'));
        $desc = Convert::raw2sql($request->postVar('description'));

        $bg = $request->postVar('bgimage');

        $client_data = $request->postVar('client');

        if (empty($this->project)) {
            $this->project = Project::create();
            $this->project->Title = $title;
        }

        if (!empty($desc)) {
            $this->project->Content = $desc;
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

            $this->project->ClientID = $client->ID;
        }

        if ($bg && '0' == $bg['error']) {
            $bg = $this->handleBGUpload($bg['tmp_name'], $bg['name']);
            $this->project->BackgroundID = $bg->ID;
        }

        $this->project->write();

        return ProjectController::create()->Data;
    }

    private function handleBGUpload($image, $filename)
    {
        $fold = Folder::find_or_make('ProjectBackgrounds');
        $img = Image::create();

        $img->setFromLocalFile($image, $filename);

        $img->ParentID = $fold->ID;
        $img->write();
        $img->publishSingle();

        AssetAdmin::create()->generateThumbnails($img);

        return $img;
    }
}
