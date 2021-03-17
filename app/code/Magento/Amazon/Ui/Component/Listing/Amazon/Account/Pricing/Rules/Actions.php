<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Pricing\Rules;

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
    const URL_EDIT_RULE = 'channel/amazon/account_pricing_rules_create';
    /** @var string */
    const URL_DELETE_RULE = 'channel/amazon/account_pricing_rules_delete';

    /** @var UrlInterface $urlBuilder */
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

                    /** @var int */
                    $id = $item['id'];
                    /** @var int */
                    $merchantId = (isset($item['merchant_id'])) ? $item['merchant_id'] : '';
                    /** @var string */
                    $message = 'If deleted, the price rule will be removed.<br><br>Are you sure you want to delete?';

                    $item[$this->getData('name')] = [
                        'edit_rule' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_EDIT_RULE,
                                ["id" => $id, "merchant_id" => $merchantId]
                            ),
                            'label' => __('Edit Price Rule')
                        ],
                        'delete_rule' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_DELETE_RULE,
                                ["id" => $id, "merchant_id" => $merchantId]
                            ),
                            'label' => __('Delete Price Rule'),
                            'confirm' => [
                                'title' => __('Are you sure you want to delete the pricing rule?'),
                                'message' => __($message)
                            ],
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
