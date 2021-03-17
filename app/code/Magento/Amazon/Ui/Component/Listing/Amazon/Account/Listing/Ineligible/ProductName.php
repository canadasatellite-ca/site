<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\Ineligible;

use Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\ProductNameSource;

/**
 * Class ProductName
 */
class ProductName extends ProductNameSource
{
    public function prepareDataSource(array $dataSource)
    {
        return $this->dataSource($dataSource, "listing_view_ineligible");
    }
}
