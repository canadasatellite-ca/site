<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\Thirdparty;

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
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['id'])) {

                    /** @var int */
                    $id = $item['id'];

                    $item[$this->getData('name')] = [
                        'assign_magento_product' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_THIRDPARTY_MANUAL,
                                [
                                    "id" => $id,
                                    "merchant_id" => $this->getMerchantId(),
                                    "tab" => "listing_view_thirdparty"
                                ]
                            ),
                            'label' => __('Assign Catalog Product')
                        ],
                        'create_magento_product' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_THIRDPARTY_CREATE,
                                [
                                    "id" => $id,
                                    "merchant_id" => $this->getMerchantId(),
                                    "tab" => "listing_view_thirdparty"
                                ]
                            ),
                            'label' => __('Create New Catalog Product')
                        ]
                    ];

                    $item[$this->getData('name')] = array_merge(
                        $item[$this->getData('name')],
                        $this->getViewDetails($id, "listing_view_thirdparty")
                    );
                }
            }
        }

        return $dataSource;
    }
}
