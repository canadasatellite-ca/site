<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Plugin\Catalog\Model\ResourceModel\Product\Option;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

/**
 * Catalog product options collection
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Option\Collection
{

    public function addDescriptionToResult($storeId)
    {
        $productOptionTitleTable = $this->getTable('catalog_product_option_title');
        $connection = $this->getConnection();
        $titleExpr = $connection->getCheckSql(
            'store_option_description.description IS NULL',
            'default_option_description.description',
            'store_option_description.description'
        );

        $this->getSelect()->join(
            ['default_option_description' => $productOptionTitleTable],
            'default_option_description.option_id = main_table.option_id',
            ['default_description' => 'description']
        )->joinLeft(
            ['store_option_description' => $productOptionTitleTable],
            'store_option_description.option_id = main_table.option_id AND ' . $connection->quoteInto(
                'store_option_description.store_id = ?',
                $storeId
            ),
            ['store_description' => 'description', 'description' => $titleExpr]
        )->where(
            'default_option_description.store_id = ?',
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );

        return $this;
    }

    public function getProductOptions($productId, $storeId, $requiredOnly = false)
    {
        $collection = $this->addFieldToFilter(
            'cpe.entity_id',
            $productId
        )->addTitleToResult(
            $storeId
        )->addDescriptionToResult(
            $storeId
        )->addPriceToResult(
            $storeId
        )->setOrder(
            'sort_order',
            'asc'
        )->setOrder(
            'title',
            'asc'
        );
        if ($requiredOnly) {
            $collection->addRequiredFilter();
        }
        $collection->addValuesToResult($storeId);
        $this->getJoinProcessor()->process($collection);
        return $collection->getItems();
    }
    private function getJoinProcessor()
    {
        if (null === $this->joinProcessor) {
            $this->joinProcessor = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface');
        }
        return $this->joinProcessor;
    }

    public function addValuesToResult($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        $optionIds = [];
        foreach ($this as $option) {
            $optionIds[] = $option->getId();
        }
        if (!empty($optionIds)) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection $values */
            $values = $this->_optionValueCollectionFactory->create();
            $values->addTitleToResult(
                $storeId
            )
                ->addRowidToResult()
                ->addPriceToResult(
                $storeId
            )->addOptionToFilter(
                $optionIds
            )->setOrder(
                'sort_order',
                self::SORT_ORDER_ASC
            )->setOrder(
                'title',
                self::SORT_ORDER_ASC
            );

            foreach ($values as $value) {
                $optionId = $value->getOptionId();
                if ($this->getItemById($optionId)) {
                    $this->getItemById($optionId)->addValue($value);
                    $value->setOption($this->getItemById($optionId));
                }
            }
        }

        return $this;
    }
}
