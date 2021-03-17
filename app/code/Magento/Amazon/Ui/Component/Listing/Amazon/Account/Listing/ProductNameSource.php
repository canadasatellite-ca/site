<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ProductNameSource
 */
class ProductNameSource extends Column
{
    public function dataSource(array $dataSource, $tab)
    {
        if (isset($dataSource['data'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $id = $item['id'];
                $merchantId = $item['merchant_id'];
                $name = $item['name'];

                $url = $this->getContext()->getUrl(
                    "channel/amazon/account_listing_details_index",
                    ["id" => $id, "merchant_id" => $merchantId, "tab" => $tab]
                );
                $item['name'] = "<a class='action-menu-item' href='" . $url . "'>" . $name . "</a>";
            }
        }
        return $dataSource;
    }
}
