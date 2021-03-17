<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Report;
/**
 * Trait QuoteReport
 * @package Cart2Quote\Quotation\Model\ResourceModel\Report
 */
trait QuoteReport
{
    /**
     * Model initialization
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init(self::QUOTATION_AGGREGATION, 'id');
		}
	}
    /**
     * Aggregate quote data by quotation created at
     *
     * @param string|int|\DateTime|array|null $from
     * @param string|int|\DateTime|array|null $to
     * @return $this
     * @throws \Exception
     */
    private function aggregate($from = null, $to = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$connection = $this->getConnection();
        $connection->beginTransaction();
        try {
            // Create date based subselect from quotation_quote table and quotation_created_at column.
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect(
                    $this->getTable('quotation_quote'),
                    'quotation_created_at',
                    'quotation_created_at',
                    $from,
                    $to
                );
            } else {
                $subSelect = null;
            }
            $this->_clearTableByDateRange($this->getMainTable(), $from, $to, $subSelect);
            $periodExpr = $connection->getDatePartSql(
                $this->getStoreTZOffsetQuery(
                    ['qq' => $this->getTable('quotation_quote')],
                    'qq.quotation_created_at',
                    $from,
                    $to
                )
            );
            // Build required columns for quotation_aggregated table
            $columns = [
                'period' => $periodExpr,
                'store_id' => 'q.store_id',
                'quote_status' => 'qq.status',
                'quotes_count' => new \Zend_Db_Expr('COUNT(qq.quote_id)'),
                'total_item_qty_quoted' => new \Zend_Db_Expr('SUM(q.total_item_qty_quoted)'),
                'total_quoted_amount' => new \Zend_Db_Expr('SUM(q.base_grand_total)'),
                'total_tax_amount' => new \Zend_Db_Expr('SUM(qa.base_tax_amount)'),
                'total_shipping_amount' => new \Zend_Db_Expr('SUM(qa.base_shipping_amount)'),
                'total_qty_quoted' => new \Zend_Db_Expr('SUM(qq.state = "open")'),
                'total_qty_proposal' => new \Zend_Db_Expr('SUM(qq.state = "pending")'),
                'total_qty_ordered' => new \Zend_Db_Expr('SUM(qq.state = "completed")'),
                'total_qty_canceled' => new \Zend_Db_Expr('SUM(qq.state = "canceled")'),
            ];
            // Select all the needed tables
            $select = $connection->select();
            $selectQuote = $connection->select();
            $selectAddress = $connection->select();
            $cols = [
                'entity_id' => 'entity_id',
                'total_item_qty_quoted' => new \Zend_Db_Expr("SUM(items_qty)"),
                'store_id' => 'store_id',
                'base_grand_total' => 'base_grand_total'
            ];
            $colsAddress = [
                'quote_id' => 'quote_id',
                'base_tax_amount' => 'base_tax_amount',
                'base_shipping_amount' => 'base_shipping_amount',
            ];
            $selectQuote->from(
                $this->getTable('quote'),
                $cols
            )->group(
                'entity_id'
            );
            $selectAddress->from(
                $this->getTable('quote_address'),
                $colsAddress
            )->where(
                'address_type = ?',
                'shipping'
            );
            $select->from(
                ['qq' => $this->getTable('quotation_quote')],
                $columns
            )->join(
                ['q' => $selectQuote],
                'q.entity_id = qq.quote_id',
                []
            )->join(
                ['qa' => $selectAddress],
                'q.entity_id = qa.quote_id',
                []
            );
            // Run the queries to the database
            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }
            $select->group([$periodExpr, 'q.store_id', 'qq.status']);
            $connection->query($select->insertFromSelect($this->getMainTable(), array_keys($columns)));
            $connection->commit();
            $this->_setFlagData(\Cart2Quote\Quotation\Model\Flag::REPORT_QUOTATION_FLAG_CODE);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this;
		}
	}
}