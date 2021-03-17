<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\Product as originClass;

/**
 * Product entity resource model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product extends originClass
{

    /**
     * Duplicate product store values
     *
     * @param int $oldId
     * @param int $newId
     * @return $this
     */
    public function duplicate($oldId, $newId)
    {
        return $this;
        $eavTables = ['datetime', 'decimal', 'int', 'text', 'varchar'];
        $connection = $this->getConnection();

        // duplicate EAV store values
        foreach ($eavTables as $suffix) {
            $tableName = $this->getTable(['catalog_product_entity', $suffix]);

            $select = $connection->select()->from(
                $tableName,
                [
                    'attribute_id',
                    'store_id',
                    $this->getLinkField() => new \Zend_Db_Expr($connection->quote($newId)),
                    'value'
                ]
            )->where(
                $this->getLinkField() . ' = ?',
                $oldId
            );


            $connection->query(
                $connection->insertFromSelect(
                    $select,
                    $tableName,
                    ['attribute_id', 'store_id', $this->getLinkField(), 'value'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_IGNORE
                )
            );
            $this->cleanAttibutes($oldId, $newId, $tableName);
        }

        // set status as disabled
        $statusAttribute = $this->getAttribute('status');
        $statusAttributeId = $statusAttribute->getAttributeId();
        $statusAttributeTable = $statusAttribute->getBackend()->getTable();
        $updateCond[] = $connection->quoteInto($this->getLinkField() . ' = ?', $newId);
        $updateCond[] = $connection->quoteInto('attribute_id = ?', $statusAttributeId);
        $connection->update(
            $statusAttributeTable,
            ['value' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED],
            $updateCond
        );

        return $this;
    }

    protected function cleanAttibutes($oldId, $newId, $tableName) {
        $connection = $this->getConnection();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/attribute_clean.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);


        $select = $connection->select()->from(
            $tableName,
            [
                'attribute_id',
                'store_id',
                'value'
            ]
        )->where(
            $this->getLinkField() . ' = ?',
            $oldId
        );

        $oldValues = array();
        $_oldValues = $this->getConnection()->fetchAll($select);
        foreach ($_oldValues as $_oldValue) {
            if ($_oldValue['store_id'] == 0) {
                $oldValues[$_oldValue['attribute_id']] = $_oldValue;
            }
        }

        $select = $connection->select()->from(
            $tableName,
            [
                'value_id',
                'attribute_id',
                'store_id',
                'value'
            ]
        )->where(
            $this->getLinkField() . ' = ?',
            $newId
        );
        $newValues = $this->getConnection()->fetchAll($select);

        foreach ($newValues as $newValue) {
            if ($newValue['store_id'] != 0) {
                if (isset($oldValues[$newValue['attribute_id']]) && $newValue['value'] == $oldValues[$newValue['attribute_id']]['value'] ) {
                    $logger->info('Product: ' . $newId . ' : ' . $newValue['attribute_id']);
                    $connection->delete(
                        $tableName,
                        [
                            'value_id = ?' => $newValue['value_id']
                        ]
                    );
                }
            }
        }
    }
}
