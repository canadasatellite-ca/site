<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Logs\View;

use Magento\Backend\Block\Widget\Tabs;

/**
 * Class Logs
 */
class Logs extends Tabs
{
    /** @var string */
    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setData('id', 'amazon_view_logs_tab');
        $this->setDestElementId('amazon_view_logs_tab_content');
    }
}
