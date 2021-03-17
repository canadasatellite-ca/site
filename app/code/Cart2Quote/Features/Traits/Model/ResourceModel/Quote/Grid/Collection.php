<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Grid;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
/**
 * Trait Collection
 *
 * @package Cart2Quote\Quotation\Model\ResourceModel\Quote\Grid
 */
trait Collection
{
    /**
     * Init collection select
     *
     * @return $this
     */
    private function _initSelect()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['q' => $this->getTable('quote')],
            'q.entity_id=main_table.quote_id',
            $cols = '*',
            $schema = null
        );
        //only visible quotes
        $this->addFieldToFilter('is_quote', ['eq' => \Cart2Quote\Quotation\Model\Quote::IS_QUOTE]);
        //Dont display original quote
        $this->addFieldToFilter('cloned_quote', ['neq' => \Cart2Quote\Quotation\Model\Quote::ORIGINAL_QUOTE]);
        $billingAliasName = 'billing_address';
        $shippingAliasName = 'shipping_address';
        $joinTable = $this->getTable('quote_address');
        $billingColumns =[];
        $shippingColumns =[];
        $columnNames = array_column($this->getConnection()->describeTable($joinTable), 'COLUMN_NAME');
        foreach ($columnNames as $columnName) {
            $billingColumnAlias = "billing_{$columnName}";
            $billingColumn = "{$billingAliasName}.{$columnName}";
            $shippingColumnAlias = "shipping_{$columnName}";
            $shippingColumn = "{$shippingAliasName}.{$columnName}";
            $billingColumns[$billingColumnAlias] = $billingColumn;
            $shippingColumns[$shippingColumnAlias] = $shippingColumn;
            $this->addFilterToMap($billingColumnAlias, $billingColumn)
                ->addFilterToMap($shippingColumnAlias, $shippingColumn);
        }
        $this->getSelect()->joinLeft(
            [$billingAliasName => $joinTable],
            "(main_table.quote_id = {$billingAliasName}.quote_id" .
            " AND {$billingAliasName}.address_type = 'billing')",
            $billingColumns
        )->joinLeft(
            [$shippingAliasName => $joinTable],
            "(main_table.quote_id = {$shippingAliasName}.quote_id" .
            " AND {$shippingAliasName}.address_type = 'shipping')",
            $shippingColumns
        );
        $this->coreResourceHelper->prepareColumnsList($this->getSelect());
        return $this;
		}
	}
}
