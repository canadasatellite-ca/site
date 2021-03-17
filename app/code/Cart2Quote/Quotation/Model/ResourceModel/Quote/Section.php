<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote;

/**
 * Section resourcemodel
 */
class Section extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Section {
        _construct as private _traitConstruct;
        unassignedExistsForQuote as private traitUnassignedExistsForQuote;
    }

    /**
     * Items table
     *
     * @var string
     */
    protected $itemsTable;

    /**
     * @var string
     */
    protected $connectionName = 'checkout';

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_traitConstruct();
    }

    /**
     * Section constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param null|string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = 'checkout'
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * @param $quoteId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function unassignedExistsForQuote($quoteId)
    {
        return $this->traitUnassignedExistsForQuote($quoteId);
    }
}
