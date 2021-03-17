<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\Ended;

use Magento\Amazon\Model\Amazon\Definitions;
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

                    /** @var array */
                    $id = $item['id'];
                    /** @var int */
                    $listStatus = $item['list_status'];

                    if ($listStatus == Definitions::ENDED_LIST_STATUS) {
                        $item[$this->getData('name')] = [
                            'view_details' => [
                                'href' => $this->urlBuilder->getUrl(
                                    static::URL_DETAILS_INDEX,
                                    [
                                        "id" => $id,
                                        "merchant_id" => $this->getMerchantId(),
                                        "tab" => "listing_view_ended"
                                    ]
                                ),
                                'label' => __('View Details')
                            ],
                            'edit_publish' => [
                                'href' => $this->urlBuilder->getUrl(
                                    static::URL_RELIST_SAVE,
                                    [
                                        "id" => $id,
                                        "merchant_id" => $this->getMerchantId(),
                                        "tab" => "listing_view_ended"
                                    ]
                                ),
                                'label' => __('Publish On Amazon')
                            ],
                            'create_alias_index' => [
                                'href' => $this->urlBuilder->getUrl(
                                    static::URL_CREATE_ALIAS,
                                    [
                                        "id" => $id,
                                        "merchant_id" => $this->getMerchantId(),
                                        "tab" => "listing_view_ended"
                                    ]
                                ),
                                'label' => __('Create Alias Seller SKU')
                            ]
                        ];
                    } else {
                        $item[$this->getData('name')] = [
                            'view_details' => [
                                'href' => $this->urlBuilder->getUrl(
                                    static::URL_DETAILS_INDEX,
                                    [
                                        "id" => $id,
                                        "merchant_id" => $this->getMerchantId(),
                                        "tab" => "listing_view_ended"
                                    ]
                                ),
                                'label' => __('View Details')
                            ],
                            'create_alias_index' => [
                                'href' => $this->urlBuilder->getUrl(
                                    static::URL_CREATE_ALIAS,
                                    [
                                        "id" => $id,
                                        "merchant_id" => $this->getMerchantId(),
                                        "tab" => "listing_view_ended"
                                    ]
                                ),
                                'label' => __('Create Alias Seller SKU')
                            ]
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}
