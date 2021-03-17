<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ConditionOverride
 */
class ConditionOverride extends Column
{
    /**
     * Prepares data source for grid
     *
     * @return array
     * @var array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data'])) {
            foreach ($dataSource['data']['items'] as & $item) {

                /** @var array */
                $conditionOverride = (isset($item['condition_override'])) ? $item['condition_override'] : false;

                if ($conditionOverride) {
                    $item['condition'] = $conditionOverride;
                }
            }
        }
        return $dataSource;
    }
}
