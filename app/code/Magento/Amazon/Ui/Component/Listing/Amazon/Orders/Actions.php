<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Orders;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Actions
 */
class Actions extends Column
{
    /** @var string */
    const URL_ORDERS_INDEX = 'channel/amazon/order_details_index';
    /** @var string */
    const URL_ORDERS_CANCEL = 'channel/amazon/order_cancel_index';
    /** @var UrlInterface */
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

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

                    /** @var string */
                    $orderId = $item['id'];

                    $options = [
                        'view_details' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_ORDERS_INDEX,
                                ["id" => $orderId]
                            ),
                            'label' => __('View Amazon Order Details')
                        ]
                    ];

                    $item[$this->getData('name')] = $options;
                }
            }
        }

        return $dataSource;
    }
}
