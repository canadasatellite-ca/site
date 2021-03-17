<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Quote\Model\ResourceModel;

/**
 * Class Quote
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Quote\Model\ResourceModel
 */
class Quote
{
    /**
     * Quote Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quoteSession;

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Quote constructor.
     *
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper
    ) {
        $this->quotationHelper = $quotationHelper;
        $this->quoteSession = $quoteSession;
    }

    /**
     * Function that make sure that this function doesn't load a quotation quote as a cart
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote $subject
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote $quote
     * @param int $customerId
     * @return \Magento\Quote\Model\Quote $quote
     */
    public function aroundLoadByCustomerId(
        $subject,
        $proceed,
        $quote,
        $customerId
    ) {
        if (!$this->quotationHelper->isFrontendEnabled()) {
            return $proceed($quote, $customerId);
        }

        $quotationQuote = $this->quoteSession->getQuotationQuote();
        $isQuotationQuote = $quote->getIsQuotationQuote();
        if ((isset($quotationQuote) && $quotationQuote > 0) || $isQuotationQuote) {
            $quotationQuote = 1;
        } else {
            $quotationQuote = 0;
        }

        $connection = $subject->getConnection();
        $select = $this->_getLoadSelect(
            'customer_id',
            $customerId,
            $quote,
            $subject
        )->where(
            'is_active = ?',
            1
        )->where(
            'is_quotation_quote = ?',
            $quotationQuote
        )->order(
            'updated_at ' . \Magento\Framework\DB\Select::SQL_DESC
        )->limit(
            1
        );

        $data = $connection->fetchRow($select);
        if ($data) {
            $quote->setData($data);
        }
        $subject->afterLoad($quote);

        return $quote;
    }

    /**
     * Get load select plugin
     * - Adds store id filter
     *
     * @param string $field
     * @param string|int $value
     * @param \Magento\Quote\Model\Quote $object
     * @param \Magento\Quote\Model\ResourceModel\Quote $subject
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect(
        $field,
        $value,
        $object,
        $subject
    ) {
        $field = $subject->getConnection()->quoteIdentifier(sprintf('%s.%s', $subject->getMainTable(), $field));
        $select = $subject->getConnection()->select()->from($subject->getMainTable())->where($field . '=?', $value);
        $storeIds = $object->getSharedStoreIds();
        if ($storeIds) {
            $select->where('store_id IN (?)', $storeIds);
        } else {
            $select->where('store_id < ?', 0);
        }

        return $select;
    }
}
