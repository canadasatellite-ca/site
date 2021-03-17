<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Form\Amazon\Account\Listing\Rules;

use Magento\Amazon\Api\ListingRuleRepositoryInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\CatalogRule\Model\Rule\WebsitesOptionsProvider;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\System\Store;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class Modifier
 */
class Modifier implements ModifierInterface
{
    /** Container fieldset prefix */
    const CONTAINER_PREFIX = 'container_';
    const META_CONFIG_PATH = '/arguments/data/config';

    const CUSTOM_MODAL_LINK = 'custom_modal_link';
    const CUSTOM_MODAL_INDEX = 'custom_modal';
    const CUSTOM_MODAL_CONTENT = 'content';
    const CUSTOM_MODAL_FIELDSET = 'fieldset';
    const CONTAINER_HEADER_NAME = 'header';

    const FIELD_NAME_1 = 'field1';
    const FIELD_NAME_2 = 'field2';
    const FIELD_NAME_3 = 'field3';

    /** @var ArrayManager $arrayManager */
    protected $arrayManager;
    /** @var Http $request */
    protected $request;
    /** @var ListingRuleRepositoryInterface $listingRuleRepository */
    protected $listingRuleRepository;
    /** @var WebsitesOptionsProvider $websitesOptionsProvider */
    protected $websitesOptionsProvider;
    /** @var Store $store */
    protected $store;

    /**
     * @var \Magento\Catalog\Model\Locator\LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ArrayManager $arrayManager
     * @param Http $request
     * @param ListingRuleRepositoryInterface $listingRuleRepository
     * @param WebsitesOptionsProvider $websitesOptionsProvider
     * @param Store $store
     */
    public function __construct(
        ArrayManager $arrayManager,
        Http $request,
        ListingRuleRepositoryInterface $listingRuleRepository,
        WebsitesOptionsProvider $websitesOptionsProvider,
        Store $store,
        LocatorInterface $locator,
        UrlInterface $urlBuilder
    ) {
        $this->arrayManager = $arrayManager;
        $this->request = $request;
        $this->listingRuleRepository = $listingRuleRepository;
        $this->websitesOptionsProvider = $websitesOptionsProvider;
        $this->store = $store;
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->prepareMerchantData($meta);

        return $meta;
    }

    /**
     * Prepare merchant data
     *
     * @param array $meta
     * @return array $meta
     */
    private function prepareMerchantData(array $meta)
    {
        /** @var int */
        $merchantId = $this->request->getParam('merchant_id');

        $meta = array_replace_recursive(
            $meta,
            [
                'rule_settings' => [
                    'children' => [
                        'merchant_id' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'value' => $merchantId
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }
}
