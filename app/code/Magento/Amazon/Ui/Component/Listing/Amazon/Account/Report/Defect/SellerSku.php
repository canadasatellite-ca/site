<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Report\Defect;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class SellerSku
 */
class SellerSku extends Column
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

                /** @var int */
                $productId = (isset($item['catalog_product_id'])) ? $item['catalog_product_id'] : false;
                /** @var string */
                $sku = (isset($item['seller_sku'])) ? $item['seller_sku'] : '';

                if ($productId) {
                    /** @var string */
                    $url = $this->getContext()->getUrl("catalog/product/edit", ["id" => $productId]);

                    $item['seller_sku'] = "<a class='action-menu-item' href='$url' target='_blank'>$sku</a>";
                } else {
                    $item['seller_sku'] = "$sku";
                }
            }
        }
        return $dataSource;
    }
}
