<?php

namespace App\Web\Controller;

use SilverStripe\Core\Convert;
use PageController;
use App\Web\Model\Client;
use Page;

class ClientController extends PageController
{
    protected $Title = 'Clients';

    public function getData()
    {
        $slug = Convert::raw2sql($this->request->param('slug'));
        $data = Page::create()->Data;

        if (!empty($slug)) {
            return [
                'slug' => $slug,
            ];
        }

        $mini = Convert::raw2sql($this->request->getVar('mini'));

        if ($mini && !empty($mini)) {
            return array_map(function($o) {
              return [
                'text' => $o->Title,
                'value' => $o->ID,
              ];
            }, Client::get()->limit(5)->toArray());
        }

        $page = Convert::raw2sql($this->request->getVar('page'));
        $page = empty($page) ? 0 : $page;

        $data['title'] = $this->Title;
        $data['pagetype'] = 'ClientList';

        return array_merge($data, [
          'list' => Client::get()->limit(12, $page)->Data,
        ]);
    }
}
