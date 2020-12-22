<?php

namespace App\Web\Controller;

use App\Web\Model\Project;
use Page;
use PageController;
use SilverStripe\Core\Convert;

class ProjectController extends PageController
{
    protected $Title = 'Projects';

    public function getData()
    {
        $slug = Convert::raw2sql($this->request->param('slug'));
        $data = Page::create()->Data;

        if (!empty($slug)) {
            return array_merge($data, $this->getProject($slug), ['pagetype' => 'Project']);
        }

        $page = Convert::raw2sql($this->request->getVar('page'));
        $page = empty($page) ? 0 : $page;

        $data['title'] = $this->Title;
        $data['pagetype'] = 'ProjectList';

        return array_merge($data, [
            'list' => Project::get()->limit(12, $page)->Data,
        ]);
    }

    private function getProject($slug)
    {
        if ($project = Project::get()->filter(['URLSegment' => $slug])->first()) {
            return $project->jsonSerialize();
        }

        return $this->httpError(404);
    }
}
