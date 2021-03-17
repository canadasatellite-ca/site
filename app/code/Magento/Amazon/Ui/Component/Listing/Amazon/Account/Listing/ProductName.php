<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ProductName
 */
class ProductName extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                /** @var int */
                $id = $item['id'];
                /** @var string */
                $merchantId = $item['merchant_id'];
                /** @var string */
                $name = $item['name'];

                /** @var string */
                $url = $this->getContext()->getUrl(
                    "channel/amazon/account_listing_details_index",
                    ["id" => $id, "merchant_id" => $merchantId]
                );
                $item['name'] = "<a class='action-menu-item' href='" . $url . "'>" . $name . "</a>";
            }
        }
        return $dataSource;
    }
}
