<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Menu\Attribute;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Actions
 */
class Actions extends Column
{
    /**
     * Prepares data source for grid
     *
     * @return array
     * @var array
     */
    public function prepareDataSource(array $dataSource)
    {
        /** @var string */
        $editSettings = __('Edit Attribute');
        /** @var string */
        $createAttribute = __('Create Attribute');

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {

                /** @var string */
                $url = $this->getContext()->getUrl(
                    "channel/amazon/attribute_value_index",
                    ["id" => $item['id'], "parent_id" => $item['id']]
                );

                if ($item['catalog_attribute']) {
                    $item['actions'] = "<a href='$url'>$editSettings</a>";
                } else {
                    $item['actions'] = "<a href='$url'>$createAttribute</a>";
                }
            }
        }

        return $dataSource;
    }
}
