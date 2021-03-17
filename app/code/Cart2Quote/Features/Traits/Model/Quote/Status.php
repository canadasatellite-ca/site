<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote;
/**
 * Trait Status
 *
 * @method string getStatus()
 * @method string getLabel()
 */
trait Status
{
    /**
     * Unassigns quote status from particular state
     *
     * @param string $state
     * @return $this
     * @throws \Exception
     */
    private function unassignState($state)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->validateBeforeUnassign($state);
        $this->getResource()->unassignState($this->getStatus(), $state);
        $this->_eventManager->dispatch(
            'quotation_quote_status_unassign',
            [
                'status' => $this->getStatus(),
                'state' => $state
            ]
        );
        return $this;
		}
	}
    /**
     * Validate before unassign
     *
     * @param string $state
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateBeforeUnassign($state)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->getResource()->checkIsStateLast($state)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The last status can\'t be unassigned from its current state.')
            );
        }
        if ($this->getResource()->checkIsStatusUsed($this->getStatus())) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Status can\'t be unassigned, because it is used by existing quote(s).')
            );
        }
		}
	}
    /**
     * Get the flag to decide whether or not the button on the customer dashboard quote detail view should be enabled
     *
     * @return string
     */
    private function getFrontendButtonHtmlFlag()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$flag = 'disabled';
        if ($this->canAccept()) {
            $flag = '';
        }
        return $flag;
		}
	}
    /**
     * Function to check whether the quote can be accepted based on its state and status
     *
     * @param null|string $state
     * @param null|string $status
     * @return bool
     */
    private function canAccept($state = null, $status = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($state == null) {
            $state = $this->getState();
        }
        if ($status == null) {
            $status = $this->getStatus();
        }
        return
            in_array($state, [
                self::STATE_PENDING,
                self::STATE_COMPLETED
            ]) &&
            in_array($status, [
                self::STATUS_PENDING,
                self::STATUS_QUOTE_AVAILABLE,
                self::STATUS_PROPOSAL_SENT,
                self::STATUS_AUTO_PROPOSAL_SENT,
                self::STATUS_ACCEPTED,
            ]);
		}
	}
    /**
     * Function to check whether the quote can show prices based on its state and status
     *
     * @param null|string $state
     * @param null|string $status
     * @return bool
     */
    private function showPrices($state = null, $status = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($state == null) {
            $state = $this->getState();
        }
        if ($status == null) {
            $status = $this->getStatus();
        }
        return
            in_array($state, [
                self::STATE_PENDING,
                self::STATE_COMPLETED
            ]) &&
            in_array($status, [
                self::STATUS_PENDING,
                self::STATUS_QUOTE_AVAILABLE,
                self::STATUS_PROPOSAL_SENT,
                self::STATUS_AUTO_PROPOSAL_SENT,
                self::STATUS_ACCEPTED,
                self::STATUS_ORDERED,
            ]);
		}
	}
    /**
     * Function to get statuses where display modified quote
     *
     * @param null|string $state
     * @param null|string $status
     * @return bool
     */
    private function showQuotableQuote($state = null, $status = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($state == null) {
            $state = $this->getState();
        }
        if ($status == null) {
            $status = $this->getStatus();
        }
        return
            in_array($state, [
                self::STATE_PENDING,
                self::STATE_COMPLETED,
                self::STATE_CANCELED
            ]) &&
            in_array($status, [
                self::STATUS_PENDING,
                self::STATUS_QUOTE_AVAILABLE,
                self::STATUS_PROPOSAL_SENT,
                self::STATUS_AUTO_PROPOSAL_SENT,
                self::STATUS_ACCEPTED,
                self::STATUS_ORDERED,
                self::STATUS_CANCELED,
                self::STATUS_OUT_OF_STOCK,
                self::STATUS_PROPOSAL_EXPIRED,
                self::STATUS_PROPOSAL_REJECTED,
                self::STATUS_CLOSED,
            ]);
		}
	}
    /**
     * Returns true if this->getStatus() is STATUS_ACCEPTED
     *
     * @return bool
     */
    private function statusIsAccepted()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getStatus() == self::STATUS_ACCEPTED;
		}
	}
    /**
     * Constructor
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init(\Cart2Quote\Quotation\Model\ResourceModel\Quote\Status::class);
		}
	}
}
