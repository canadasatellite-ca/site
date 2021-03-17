<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\View;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * Class Tabs
 */
class Tabs extends WidgetTabs
{
    /** @var string */
    protected $_template = 'Magento_Amazon::widget/tabshoriz.phtml';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setData('id', 'amazon_account_listing_view_tabs');
        $this->setDestElementId('amazon_account_listing_index');
    }
}
