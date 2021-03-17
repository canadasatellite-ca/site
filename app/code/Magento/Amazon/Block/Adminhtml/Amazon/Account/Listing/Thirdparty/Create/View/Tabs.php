<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\Thirdparty\Create\View;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * Class Tabs
 */
class Tabs extends WidgetTabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setData('id', 'amazon_account_listing_thirdparty_create_view_tabs');
        $this->setDestElementId('amazon_account_listing_thirdparty_create_index');
        $this->setData('title', __('Third Party Imported Listings'));
    }
}
