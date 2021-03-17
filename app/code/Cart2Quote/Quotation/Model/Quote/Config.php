<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote;

use Cart2Quote\Quotation\Model\Quote\Status as QuoteStatus;

/**
 * Quote configuration model
 */
class Config
{
    use \Cart2Quote\Features\Traits\Model\Quote\Config {
        getStateDefaultStatus as private traitGetStateDefaultStatus;
        _getState as private _traitGetState;
        _getCollection as private _traitGetCollection;
        getStatusLabel as private traitGetStatusLabel;
        maskStatusForArea as private traitMaskStatusForArea;
        getStateLabel as private traitGetStateLabel;
        getStatuses as private traitGetStatuses;
        _getStatuses as private _traitGetStatuses;
        getStates as private traitGetStates;
        getStateStatuses as private traitGetStateStatuses;
        getVisibleOnFrontStatuses as private traitGetVisibleOnFrontStatuses;
        getInvisibleOnFrontStatuses as private traitGetInvisibleOnFrontStatuses;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\Collection
     */
    protected $collection;

    /**
     * Statuses per state array
     *
     * @var array
     */
    protected $stateStatuses;

    /**
     * @var Status
     */
    protected $quoteStatusFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\CollectionFactory
     */
    protected $quoteStatusCollectionFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var array
     */
    protected $maskStatusesMapping = [
        \Magento\Framework\App\Area::AREA_FRONTEND => [
            QuoteStatus::STATUS_OPEN => QuoteStatus::STATUS_PROCESSING,
            QuoteStatus::STATUS_NEW => QuoteStatus::STATUS_PROCESSING,
            QuoteStatus::STATUS_CHANGE_REQUEST => QuoteStatus::STATUS_PROCESSING,
            QuoteStatus::STATUS_PENDING => QuoteStatus::STATUS_QUOTE_AVAILABLE,
            QuoteStatus::STATUS_PROPOSAL_SENT => QuoteStatus::STATUS_QUOTE_AVAILABLE,
        ],
    ];

    /**
     * @var array
     */
    protected $statuses;

    /**
     * Config constructor
     *
     * @param \Cart2Quote\Quotation\Model\Quote\StatusFactory $quoteStatusFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\CollectionFactory $quoteStatusCollectionFactory
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\StatusFactory $quoteStatusFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\CollectionFactory $quoteStatusCollectionFactory,
        \Magento\Framework\App\State $state
    ) {
        $this->quoteStatusFactory = $quoteStatusFactory;
        $this->quoteStatusCollectionFactory = $quoteStatusCollectionFactory;
        $this->state = $state;
    }

    /**
     * Retrieve default status for state
     *
     * @param   string $state
     * @return  string
     */
    public function getStateDefaultStatus($state)
    {
        return $this->traitGetStateDefaultStatus($state);
    }

    /**
     * Get state object by state code
     *
     * @param string $state
     * @return Status|null
     */
    protected function _getState($state)
    {
        return $this->_traitGetState($state);
    }

    /**
     * Get collection
     *
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\Collection
     */
    protected function _getCollection()
    {
        return $this->_traitGetCollection();
    }

    /**
     * Retrieve status label
     *
     * @param   string $code
     * @return  string
     */
    public function getStatusLabel($code)
    {
        return $this->traitGetStatusLabel($code);
    }

    /**
     * Mask status for quote for specified area
     *
     * @param string $area
     * @param string $code
     * @return string
     */
    protected function maskStatusForArea($area, $code)
    {
        return $this->traitMaskStatusForArea($area, $code);
    }

    /**
     * State label getter
     *
     * @param string $state
     * @return \Magento\Framework\Phrase|string
     */
    public function getStateLabel($state)
    {
        return $this->traitGetStateLabel($state);
    }

    /**
     * Retrieve all statuses
     *
     * @return array
     */
    public function getStatuses()
    {
        return $this->traitGetStatuses();
    }

    /**
     * Get existing quote statuses
     * - Visible or invisible on frontend according to passed param
     *
     * @param bool $visibility
     * @return array
     */
    protected function _getStatuses($visibility)
    {
        return $this->_traitGetStatuses($visibility);
    }

    /**
     * Quote states getter
     *
     * @return array
     */
    public function getStates()
    {
        return $this->traitGetStates();
    }

    /**
     * Retrieve statuses available for state
     * - Get all possible statuses, or for specified state, or specified states array
     * - Add labels by default. Return plain array of statuses, if no labels.
     *
     * @param mixed $state
     * @param bool $addLabels
     * @return array
     */
    public function getStateStatuses($state, $addLabels = true)
    {
        return $this->traitGetStateStatuses($state, $addLabels);
    }

    /**
     * Retrieve states which are visible on front end
     *
     * @return array
     */
    public function getVisibleOnFrontStatuses()
    {
        return $this->traitGetVisibleOnFrontStatuses();
    }

    /**
     * Get quote statuses, invisible on frontend
     *
     * @return array
     */
    public function getInvisibleOnFrontStatuses()
    {
        return $this->traitGetInvisibleOnFrontStatuses();
    }
}
