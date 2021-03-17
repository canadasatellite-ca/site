<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class AbstractActions
 */
class AbstractActions extends Column
{
    /** @var string */
    const URL_DETAILS_INDEX = 'channel/amazon/account_listing_details_index';
    /** @var string */
    const URL_RELIST_SAVE = 'channel/amazon/account_listing_relist';
    /** @var string */
    const URL_OVERRIDES_INDEX = 'channel/amazon/account_listing_overrides_index';
    /** @var string */
    const URL_MANUALLIST_SAVE = 'channel/amazon/account_listing_manualList';
    /** @var string */
    const URL_UPDATE_INDEX = 'channel/amazon/account_listing_update_index';
    /** @var string */
    const URL_THIRDPARTY_MANUAL = 'channel/amazon/account_listing_thirdparty_manual';
    /** @var string */
    const URL_THIRDPARTY_CREATE = 'channel/amazon/account_listing_thirdparty_create';
    /** @var string */
    const URL_CREATE_ALIAS = 'channel/amazon/account_listing_alias_index';
    /** @var string */
    const URL_EDIT_ASIN = 'channel/amazon/account_listing_update_index';
    /** @var string */
    const URL_END_LISTING = 'channel/amazon/account_listing_endListing';
    /** @var string */
    const URL_FULFILLED_BY = 'channel/amazon/account_listing_editFulfillment';

    /** @var UrlInterface $urlBuilder */
    protected $urlBuilder;
    /** @var RequestInterface $request */
    protected $request;

    /**
     * Action constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param RequestInterface $request
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        RequestInterface $request,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Returns merchant id
     *
     * @return int
     */
    protected function getMerchantId()
    {
        return $this->request->getParam('merchant_id');
    }

    /**
     * Returns viewDetails ui action
     *
     * @param int $id
     * @param string $tab
     * @return array
     */
    protected function getViewDetails($id, $tab)
    {
        return [
            'view_details' => [
                'href' => $this->urlBuilder->getUrl(
                    static::URL_DETAILS_INDEX,
                    ["id" => $id, "merchant_id" => $this->getMerchantId(), "tab" => $tab]
                ),
                'label' => __('View Details')
            ]
        ];
    }

    /**
     * Returns overrides ui action
     *
     * @param int $id
     * @return array
     */
    protected function getOverrides($id, $tab)
    {
        return [
            'edit_overrides' => [
                'href' => $this->urlBuilder->getUrl(
                    static::URL_OVERRIDES_INDEX,
                    ["id" => $id, "merchant_id" => $this->getMerchantId(), "tab" => $tab]
                ),
                'label' => __('Edit Overrides')
            ]
        ];
    }

    /**
     * Prepares data source for grid
     *
     * @return array
     * @var array
     */
    public function dataSource(array $dataSource, $tab)
    {
        /** @var int */
        $merchantId = $this->request->getParam('merchant_id');

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['id'])) {

                    /** @var int */
                    $id = $item['id'];
                    /** @var string */
                    $fulfilledBy = (isset($item['fulfilled_by'])) ? $item['fulfilled_by'] : 'DEFAULT';

                    $item[$this->getData('name')] = [
                        'view_details' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_DETAILS_INDEX,
                                ["id" => $id, "merchant_id" => $merchantId, "tab" => $tab]
                            ),
                            'label' => __('View Details')
                        ],
                        'overrides_index' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_OVERRIDES_INDEX,
                                ["id" => $id, "merchant_id" => $merchantId, "tab" => $tab]
                            ),
                            'label' => __('Create Override')
                        ],
                        'edit_asin_index' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_EDIT_ASIN,
                                ["id" => $id, "merchant_id" => $merchantId, "tab" => $tab]
                            ),
                            'label' => __('Edit Assigned ASIN')
                        ],
                        'create_alias_index' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_CREATE_ALIAS,
                                ["id" => $id, "merchant_id" => $merchantId, "tab" => $tab]
                            ),
                            'label' => __('Create Alias Seller SKU')
                        ],
                        'edit_fullfillment_save' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_FULFILLED_BY,
                                ["id" => $id, "merchant_id" => $merchantId, "tab" => $tab]
                            ),
                            'label' => (strpos($fulfilledBy, 'AMAZON') !== false) ?
                                __('Switch to Fulfilled By Merchant') : __('Switch To Fulfilled By Amazon'),
                            'confirm' => [
                                'title' => __('<b>Are you sure you want to change the fulfillment type?</b>'),
                                'message' => __('If changing to Fulfilled By Amazon, you will still need to log into '
                                    . 'your seller central account and complete the process of creating Fulfilled By '
                                    . 'Amazon inventory (if not already done).')
                            ]
                        ],
                        'end_listing' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_END_LISTING,
                                ["id" => $id, "merchant_id" => $merchantId, "tab" => $tab]
                            ),
                            'label' => __('End Listing'),
                            'confirm' => [
                                'title' => __('<b>Are you sure you want to end the listing?</b>'),
                                'message' => __('If ended, the listing will removed from Amazon until manually '
                                    . 'published.<br><br>Are you sure you want to end the listing?')
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
