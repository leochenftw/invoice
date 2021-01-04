<?php

namespace App\Web\Admin;

use App\Web\Model\Client;
use SilverStripe\Admin\ModelAdmin;

/**
 * Description.
 */
class ClientAdmin extends ModelAdmin
{
    /**
     * Managed data objects for CMS.
     *
     * @var array
     */
    private static $managed_models = [
        Client::class,
    ];

    /**
     * URL Path for CMS.
     *
     * @var string
     */
    private static $url_segment = 'clients';

    /**
     * Menu title for Left and Main CMS.
     *
     * @var string
     */
    private static $menu_title = 'Clients';
}
