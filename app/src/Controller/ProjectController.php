<?php

namespace App\Web\Controller;

use SilverStripe\Core\Convert;
use PageController;
use App\Web\Model\Project;
use Page;

class ProjectController extends PageController
{
    protected $Title = 'Projects';

    public function getData()
    {
        $slug = Convert::raw2sql($this->request->param('slug'));
        $data = Page::create()->Data;

        if (!empty($slug)) {
            return [
                'slug' => $slug,
            ];
        }

        $page = Convert::raw2sql($this->request->getVar('page'));
        $page = empty($page) ? 0 : $page;

        $data['title'] = $this->Title;
        $data['pagetype'] = 'ProjectList';

        return array_merge($data, [
          'list' => Project::get()->limit(12, $page)->Data,
        ]);
    }
}
