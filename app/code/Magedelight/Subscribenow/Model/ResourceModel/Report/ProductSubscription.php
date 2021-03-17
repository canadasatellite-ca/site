<?php

/**
 * Magedelight
 * Copyright (C) 2017 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Model\ResourceModel\Report;

/**
 * ProductSubscription report resource model.
 *
 * @SuppressWarnings(PHPMagedelight.CouplingBetweenObjects)
 */
class ProductSubscription extends \Magento\Sales\Model\ResourceModel\Report\AbstractReport
{

    /**
     * Model initialization.
     */
    protected function _construct()
    {
        $this->_init('md_subscribenow_aggregated_product', 'id');
    }

    /**
     * Aggregate subscription product by subscription created at.
     *
     * @param string|int|\DateTime|array|null $from
     * @param string|int|\DateTime|array|null $to
     *
     * @return $this
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMagedelight.ExcessiveMethodLength)
     */
    public function aggregate($from = null, $to = null)
    {
        $this->_aggregateBySubscriptionProduct($from, $to);
        $this->_setFlagData(\Magedelight\Subscribenow\Model\Flag::REPORT_PRODUCT_SUBSCRIPTION_FLAG_CODE);

        return $this;
    }

    /**
     * Aggregate subscription product by create_at as period.
     *
     * @param string|null $from
     * @param string|null $to
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function _aggregateBySubscriptionProduct($from, $to)
    {
        $table = $this->getTable('md_subscribenow_aggregated_product');
        $sourceTable = $this->getTable('md_subscribenow_product_subscribers');
        $connection = $this->getConnection();
        $connection->beginTransaction();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productnameattributeId = $objectManager->create('Magento\Eav\Model\Config')->getAttribute('catalog_product', 'name')->getAttributeId();
        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect($sourceTable, 'created_at', 'updated_at', $from, $to);
            } else {
                $subSelect = null;
            }
            $this->_clearTableByDateRange($table, $from, $to, $subSelect);

            // convert dates to current admin timezone
            $periodExpr = $connection->getDatePartSql(
                $this->getStoreTZOffsetQuery($sourceTable, 'created_at', $from, $to)
            );

            $columns = [
                'period' => $periodExpr,
                'store_id' => 'store_id',
                'product_id' => 'product_id',
                'subscriber_count' => new \Zend_Db_Expr('COUNT(md_subscribenow_product_subscribers.subscription_id)'),
                'active_subscriber' => new \Zend_Db_Expr(
                    'SUM(subscription_status = 1)'
                ),
                'pause_subscriber' => new \Zend_Db_Expr(
                    'SUM(subscription_status = 2)'
                ),
                'cancel_subscriber' => new \Zend_Db_Expr(
                    'SUM(subscription_status = 4)'
                ),
                    //     'no_of_occurrence' => new \Zend_Db_Expr('COUNT(mdspo.occurrence_id)'),
            ];

            $objManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->productMetadata = $objManager->get('Magento\Framework\App\ProductMetadataInterface');

            $select = $connection->select();

            if ($this->productMetadata->getEdition() == 'Community') {
                $select->from(
                    $sourceTable,
                    $columns
                )
                        ->join(
                            ['mdcpev' => $this->getTable('catalog_product_entity_varchar')],
                            "mdcpev.entity_id = product_id and mdcpev.attribute_id=$productnameattributeId ",
                            []
                        );
            } else {
                $select->from(
                    $sourceTable,
                    $columns
                )
                        ->join(
                            ['mdcpev' => $this->getTable('catalog_product_entity_varchar')],
                            "mdcpev.row_id = product_id and mdcpev.attribute_id=$productnameattributeId ",
                            []
                        );
            }

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }
            $select->group([$periodExpr, 'store_id', 'product_id']);
            $select->having('subscriber_count > 0');

            $insertQuery = $select->insertFromSelect($table, array_keys($columns));
            $connection->query($insertQuery);
            $select->reset();

            $columns = [
                'period' => 'period',
                'store_id' => new \Zend_Db_Expr(\Magento\Store\Model\Store::DEFAULT_STORE_ID),
                'product_id' => 'product_id',
                'product_name' => new \Zend_Db_Expr('MIN(product_name)'),
                'subscriber_count' => new \Zend_Db_Expr('COUNT(subscriber_count)'),
                'active_subscriber' => new \Zend_Db_Expr('SUM(active_subscriber)'),
                'pause_subscriber' => new \Zend_Db_Expr('SUM(pause_subscriber)'),
                'cancel_subscriber' => new \Zend_Db_Expr('SUM(cancel_subscriber)'),
                    //   'no_of_occurrence' => new \Zend_Db_Expr('COUNT(no_of_occurrence)'),
            ];
            $select->from($table, $columns)->where('store_id != ?', \Magento\Store\Model\Store::DEFAULT_STORE_ID);
            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }
            $select->group(['period', 'product_id']);
            $insertQuery = $select->insertFromSelect($table, array_keys($columns));
            $connection->query($insertQuery);
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        $connection->commit();

        return $this;
    }
}
