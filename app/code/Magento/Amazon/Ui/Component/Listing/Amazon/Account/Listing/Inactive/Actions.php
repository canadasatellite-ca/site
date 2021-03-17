<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\Inactive;

use Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\AbstractActions;

/**
 * Class Actions
 */
class Actions extends AbstractActions
{
    /**
     * Prepares data source for grid
     *
     * @return array
     * @var array
     */
    public function prepareDataSource(array $dataSource)
    {
        return $this->dataSource($dataSource, "listing_view_inactive");
    }
}
