<?php

namespace App\Web\Admin;

use App\Web\Model\Project;
use SilverStripe\Admin\ModelAdmin;

/**
 * Description.
 */
class ProjectAdmin extends ModelAdmin
{
    /**
     * Managed data objects for CMS.
     *
     * @var array
     */
    private static $managed_models = [
        Project::class,
    ];

    /**
     * URL Path for CMS.
     *
     * @var string
     */
    private static $url_segment = 'projects';

    /**
     * Menu title for Left and Main CMS.
     *
     * @var string
     */
    private static $menu_title = 'Projects';
}
