<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Reports\Model\ResourceModel\Order;

/**
 * Class CollectionPlugin
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Reports\Model\ResourceModel\Order
 */
class CollectionPlugin
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection
     */
    protected $quoteResource;

    /**
     * CollectionPlugin constructor
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $quoteResource
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $quoteResource
    ) {
        $this->quoteResource = $quoteResource;
    }

    /**
     * Plugin style version of _afterLoad()
     *
     * @param $subject \Magento\Reports\Model\ResourceModel\Order\Collection
     * @param callable $proceed
     * @param bool $printQuery
     * @param bool $logQuery
     * @return \Magento\Reports\Model\ResourceModel\Order\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundLoad(
        $subject,
        $proceed,
        $printQuery = false,
        $logQuery = false
    ) {
        $before = $subject->isLoaded();
        $result = $proceed($printQuery, $logQuery);
        $after = $subject->isLoaded();

        //plugin style version of _afterLoad()
        if ($before != $after) {
            $this->_addQuotesStatistics($result);
        }

        return $result;
    }

    /**
     * Add quotes statistics to collection items
     *
     * @param $result \Magento\Reports\Model\ResourceModel\Order\Collection
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addQuotesStatistics($result)
    {
        //only execute when group by is customer_id
        $group = $result->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);
        if (!$group || !is_array($group) || !in_array('main_table.customer_id', $group)) {
            return $this;
        }

        $customerIds = $result->getColumnValues('customer_id');
        if (!empty($customerIds)) {
            $select = $this->quoteResource->getConnection()->select();
            $select->from(
                ['main_table' => $this->quoteResource->getTable('quotation_quote')],
                [
                    'quotes_count' => 'COUNT(main_table.quote_id)'
                ]
            )->joinLeft(
                ['quote' => $this->quoteResource->getTable('quote')],
                'quote.entity_id=main_table.quote_id',
                'customer_id'
            )->where(
                'main_table.state NOT IN (?)',
                [
                    \Cart2Quote\Quotation\Model\Quote\Status::STATUS_NEW,
                    \Cart2Quote\Quotation\Model\Quote\Status::STATE_CANCELED
                ]
            )->where(
                'quote.customer_id IN(?)',
                $customerIds
            )->group(
                'quote.customer_id'
            );

            foreach ($this->quoteResource->getConnection()->fetchAll($select) as $quotesInfo) {
                $result->getItemByColumnValue(
                    'customer_id',
                    $quotesInfo['customer_id']
                )->addData($quotesInfo);
            }
        }

        return $this;
    }
}