<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Controller\Adminhtml\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ObjectManager;

class MassConvertToBundle extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    private $indexerRegistry;

    /**
     * @param Context $context
     * @param Builder $productBuilder
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->indexerRegistry = $indexerRegistry;
        parent::__construct($context, $productBuilder);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $productConverted = 0;
        $connection = $collection->getConnection();
        $ids = [];
        $childId = 7825;
        foreach ($collection->getItems() as $product) {
            if ($product->getTypeId() == 'simple') {
                $connection->beginTransaction();
                try {
                    $id = $product->getEntityId();

                    $connection->query('INSERT INTO catalog_product_entity_int (attribute_id,store_id,entity_id,value) VALUES (123,0,?,1)' .
                        ' ON DUPLICATE KEY UPDATE value=VALUES(value)', $id);
                    $connection->query('INSERT IGNORE INTO catalog_product_bundle_option (parent_id,required,position,type) VALUES (?,1,1,"select")', $id);
                    $optionId = $connection->fetchOne('SELECT LAST_INSERT_ID()');
                    $connection->query('INSERT IGNORE INTO catalog_product_bundle_option_value (option_id,store_id,title) VALUES (?,0,"Placeholder")', $optionId);
                    $connection->insert($connection->getTableName('catalog_product_bundle_selection'),
                        array(
                            'option_id' => $optionId,
                            'parent_product_id' => $id,
                            'product_id' => $childId,
                            'position' => 1,
                            'is_default' => 1,
                            'selection_price_type' => 0,
                            'selection_price_value' => 0,
                            'selection_qty' => 1,
                            'selection_can_change_qty' => 0
                        )
                    );
                    $productConverted++;
                    $connection->query('UPDATE catalog_product_entity SET type_id="bundle" WHERE entity_id IN (?)', $id);

                    $connection->query('INSERT INTO catalog_product_relation (parent_id,child_id) VALUES ('.$id.','.$childId.')' );

                    $connection->commit();
                    $ids[] = $id;
                } catch (\Exception $e) {
                    $connection->rollBack();
                    continue;
                }

            }
        }
        if($ids){
            $this->reindex($ids);
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been converted.', $productConverted)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('catalog/*/index');
    }

    public function reindex($ids)
    {
        $productCategoryIndexer = $this->indexerRegistry->get('catalog_product_price');
        $productCategoryIndexer->reindexList($ids);

        $productCategoryIndexer = $this->indexerRegistry->get('cataloginventory_stock');
        $productCategoryIndexer->reindexList($ids);
    }
}
