<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Listing;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Log
 *
 */
class Log extends AbstractModel
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Listing\Log::class
        );
    }
}
