<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\Published;

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

                    $item[$this->getData('name')] = [
                        'manual_list' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_MANUALLIST_SAVE,
                                [
                                    "id" => $id,
                                    "merchant_id" => $this->getMerchantId(),
                                    "tab" => "listing_view_published"
                                ]
                            ),
                            'label' => __('Publish On Amazon')
                        ]
                    ];

                    $item[$this->getData('name')] =
                        array_merge(
                            $item[$this->getData('name')],
                            $this->getViewDetails($id, "listing_view_published")
                        );
                }
            }
        }

        return $dataSource;
    }
}
