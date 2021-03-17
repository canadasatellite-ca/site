<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\Active;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class BuyBoxWonLink
 */
class BuyBoxWonLink extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $id = $item['id'];
                $merchantId = $item['merchant_id'];

                $url = $this->getContext()->getUrl(
                    "channel/amazon/account_listing_details_index/active_tab/listing_details_view_bbb",
                    ["id" => $id, "merchant_id" => $merchantId, "tab" => "listing_view_active"]
                );

                $is_seller = $item['is_seller'];
                if ($is_seller === '1') {
                    $item['is_seller'] = "<a class='action-menu-item' href='$url'>Yes</a>";
                } elseif ($item['is_seller'] === '0') {
                    $item['is_seller'] = "<a class='action-menu-item' href='$url'>No</a>";
                } else {
                    $item['is_seller'] = 'NA';
                }
            }
        }
        return $dataSource;
    }
}
