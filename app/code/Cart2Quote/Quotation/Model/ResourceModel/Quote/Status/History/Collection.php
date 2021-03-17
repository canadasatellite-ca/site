<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History;

use Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection\AbstractCollection;
use Cart2Quote\Quotation\Api\Data\QuoteStatusHistorySearchResultInterface;

/**
 * Flat quotation quote status history collection
 */
class Collection extends AbstractCollection implements QuoteStatusHistorySearchResultInterface
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Status\History\Collection {
        getUnnotifiedForInstance as private traitGetUnnotifiedForInstance;
        _construct as private _traitConstruct;
    }

    /**
     * @var string
     */
    protected $connectionName = 'checkout';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'quotation_quote_status_history_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'quote_status_history_collection';

    /**
     * Get history object collection for specified instance (quote, shipment, invoice or credit memo)
     * - Parameter instance may be one of the following types: \Cart2Quote\Quotation\Model\Quote
     *
     * @param \Magento\Sales\Model\AbstractModel $instance
     * @return \Cart2Quote\Quotation\Model\Quote\Status\History|null
     */
    public function getUnnotifiedForInstance($instance)
    {
        return $this->traitGetUnnotifiedForInstance($instance);
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_traitConstruct();
    }
}
