<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Order;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Reserve
 */
class Reserve extends AbstractModel
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Order\Reserve::class
        );
    }
}
