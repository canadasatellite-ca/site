<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\Overrides\View\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\View\Element\Text\ListText;

/**
 * Class Overrides
 */
class Overrides extends ListText implements TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Listing Overrides');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Listing Overrides');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
