<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Form\Amazon\Account\Listing\Thirdparty\Create;

use Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\System\Store;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class Modifier
 */
class Modifier implements ModifierInterface
{
    /** @var Http $request */
    protected $request;
    /** @var Filter $filter */
    protected $filter;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;
    /** @var Store $store */
    protected $store;

    /**
     * @param Http $request
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Store $store
     */
    public function __construct(
        Http $request,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Store $store
    ) {
        $this->request = $request;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->store = $store;
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
        /** @var array */
        $ids = [];

        $meta['newproduct']['children']['merchant_id']['arguments']['data']['config'] = [
            'componentType' => 'field',
            'filter' => 'text',
            'formElement' => 'hidden',
            'value' => $this->request->getParam('merchant_id')
        ];

        if ($id = $this->request->getParam('id')) {
            $meta['newproduct']['children']['listing_id']['arguments']['data']['config'] = [
                'componentType' => 'field',
                'filter' => 'text',
                'formElement' => 'hidden',
                'value' => $id
            ];
        } else {
            if ($collection = $this->getFilteredCollection($this->request->getParam('merchant_id'))) {
                foreach ($collection as $listing) {
                    $ids[] = $listing->getId();
                }
            }

            $meta['newproduct']['children']['selected_ids']['arguments']['data']['config'] = [
                'componentType' => 'field',
                'filter' => 'text',
                'formElement' => 'hidden',
                'value' => json_encode($ids)
            ];
        }

        // Defaults Websites selection if only one is available
        /** @var array */
        $websites = $this->store->getWebsiteValuesForForm();
        if (count($websites) == 1) {
            $meta['newproduct']['children']['website_ids']['arguments']['data']['config']['default'] =
                $websites[0]['value'];
        }

        return $meta;
    }

    /**
     * Filters mass action collection based on user selections
     *
     * @return bool|\Magento\Amazon\Model\ResourceModel\Amazon\Listing\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     * @var int $merchantId
     */
    public function getFilteredCollection($merchantId)
    {
        $component = $this->filter->getComponent();
        $this->filter->prepareComponent($component);
        $dataProvider = $component->getContext()->getDataProvider();
        $dataProvider->setLimit(0, false);

        /** @var array */
        $ids = [];

        foreach ($dataProvider->getSearchResult()->getItems() as $document) {
            $ids[] = $document->getId();
        }

        /** @var CollectionFactory */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('id', ['in' => $ids]);
        $selected = $this->request->getParam('selected');
        $excluded = $this->request->getParam('excluded');

        $collection->addFieldToFilter('merchant_id', $merchantId);

        if ('false' !== $excluded) {
            if (is_array($excluded) && !empty($excluded)) {
                $collection->addFieldToFilter('id', ['nin' => $excluded]);
            } elseif (is_array($selected) && !empty($selected)) {
                $collection->addFieldToFilter('id', ['in' => $selected]);
            } else {
                return false;
            }
        }

        if (count($collection)) {
            return $collection;
        }

        return false;
    }
}
