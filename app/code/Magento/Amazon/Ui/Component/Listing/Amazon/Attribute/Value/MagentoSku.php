<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Attribute\Value;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ProductName
 */
class MagentoSku extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data'])) {
            foreach ($dataSource['data']['items'] as & $item) {

                /** @var int */
                $id = $item['catalog_product_id'];
                /** @var string */
                $sku = $item['catalog_sku'];

                /** @var string */
                $url = $this->getContext()->getUrl("catalog/product/edit", ["id" => $id]);
                $item['catalog_sku'] = "<a target='_blank' class='action-menu-item' href='$url'>$sku</a>";
            }
        }
        return $dataSource;
    }
}
