<?php

namespace App\Web\Admin;

use App\Web\Model\Invoice;
use SilverStripe\Admin\ModelAdmin;

/**
 * Description.
 */
class InvoiceAdmin extends ModelAdmin
{
    /**
     * Managed data objects for CMS.
     *
     * @var array
     */
    private static $managed_models = [
        Invoice::class,
    ];

    /**
     * URL Path for CMS.
     *
     * @var string
     */
    private static $url_segment = 'invoices';

    /**
     * Menu title for Left and Main CMS.
     *
     * @var string
     */
    private static $menu_title = 'Invoices';
}
