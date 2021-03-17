<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\DataProvider;

use Magento\Amazon\GraphQl\FilterToCollectionFilterConverter;
use Magento\Amazon\Model\ResourceModel\Amazon\Attribute\Collection as AttributeCollection;
use Magento\Amazon\Model\ResourceModel\Amazon\Attribute\CollectionFactory as AttributesCollectionFactory;
use Magento\Amazon\Ui\AdminStorePageUrl;

class Attributes
{
    /**
     * @var AttributesCollectionFactory
     */
    private $attributesCollectionFactory;
    /**
     * @var FilterToCollectionFilterConverter
     */
    private $filterConverter;
    /**
     * @var AdminStorePageUrl
     */
    private $adminStorePageUrl;

    public function __construct(
        AttributesCollectionFactory $attributesCollectionFactory,
        FilterToCollectionFilterConverter $filterConverter,
        AdminStorePageUrl $adminStorePageUrl
    ) {
        $this->attributesCollectionFactory = $attributesCollectionFactory;
        $this->filterConverter = $filterConverter;
        $this->adminStorePageUrl = $adminStorePageUrl;
    }

    public function resolveAttributes(AttributesRequest $request): AttributesResult
    {
        $collection = $this->getCollectionWithoutPagination($request);
        $totalCount = $request->isCalculateTotalCount() ? $this->getTotalCount($collection) : null;
        $this->applyPagination($collection, $request);
        $attributesData = $this->getAttributesData($collection, $request);
        return new AttributesResult($attributesData, $totalCount, 1 < $collection->getLastPageNumber());
    }

    private function getAttributesData(AttributeCollection $collection, AttributesRequest $request): array
    {
        $fields = $request->getAttributeFields();
        $edgeFields = $request->getEdgeFields();
        $needsCursor = isset($edgeFields['cursor']);
        $needsActionUrl = isset($fields['actionUrl']);
        $attributesData = $collection->getData();
        $results = [];
        foreach ($attributesData as $key => $attribute) {
            $data = [
                'node' => $attribute,
            ];
            if ($needsActionUrl) {
                $data['node']['actionUrl'] = $this->adminStorePageUrl->attributePage((string) $attribute['id']);
            }
            if ($needsCursor) {
                $data['cursor'] = $this->getCursor($attribute);
            }
            $results[$key] = $data;
        }
        if (!$needsCursor
            && isset($key, $attribute)
            && $request->getPageInfoFields()
        ) {
            $results[$key]['cursor'] = $this->getCursor($attribute);
        }
        return $results;
    }

    private function getCollectionWithoutPagination(AttributesRequest $request): AttributeCollection
    {
        /** @var AttributeCollection $collection */
        $collection = $this->attributesCollectionFactory->create();
        $fieldsMap = [
            'marketplace' => 'country_code',
            'amazonAttributeName' => 'amazon_attribute',
            'productCatalogAttributeCode' => 'catalog_attribute',
            'overwriteMagentoValues' => 'overwrite',
            'isActive' => 'is_active',
        ];
        // merge always loaded fields with requested fields
        $selectFields = array_merge([
            'id' => 'id',
        ], array_intersect_key($fieldsMap, $request->getAttributeFields()));
        $collection->addFieldToSelect($selectFields);

        $filters = $this->filterConverter->convert($request->getFilters(), $fieldsMap);
        foreach ($filters as $field => $conditions) {
            $collection->addFieldToFilter($field, $conditions);
        }

        return $collection;
    }

    private function applyPagination(AttributeCollection $collection, AttributesRequest $request): void
    {
        if ($request->getLimit()) {
            $collection->setPageSize($request->getLimit());
        }
        if ($request->getCursor()) {
            $lastId = base64_decode($request->getCursor());
            $collection->addFieldToFilter('id', ['gt' => $lastId]);
        }
    }

    /**
     * @param AttributeCollection $collection
     * @return int
     */
    private function getTotalCount(AttributeCollection $collection): int
    {
        $clone = clone $collection;
        return $clone->getSize();
    }

    /**
     * @param $attribute
     * @return string
     */
    private function getCursor($attribute): string
    {
        return base64_encode($attribute['id']);
    }
}
