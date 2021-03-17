<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Orders;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class SalesOrderNumber
 */
class SalesOrderNumber extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $mageOrderNumber = $item['sales_order_number'];

                /** @var string */
                if ($orderId = $item['sales_order_id']) {
                    /** @var string */
                    $url = $this->getContext()->getUrl("sales/order/view", ["order_id" => $orderId]);
                    $item['sales_order_number'] =
                        "<a target='_blank' class='action-menu-item' href='$url'>$mageOrderNumber</a>";
                }
            }
        }
        return $dataSource;
    }
}
