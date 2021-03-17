<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Pricing;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Lowest
 *
 */
class Lowest extends AbstractModel
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Lowest::class
        );
    }
}
