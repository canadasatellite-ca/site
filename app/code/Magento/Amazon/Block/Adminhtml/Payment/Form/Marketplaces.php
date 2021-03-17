<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Payment\Form;

use Magento\Payment\Block\Info;

/**
 * Class Marketplaces
 */
class Marketplaces extends Info
{
    /**
     * Template for custom payment type
     */
    protected $_template = 'Magento_Amazon::form/marketplaces.phtml';

    /**
     * Check whether payment information should show up in secure mode
     * true => only "public" payment information may be shown
     * false => full information may be shown
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsSecureMode()
    {
        return false;
    }
}
