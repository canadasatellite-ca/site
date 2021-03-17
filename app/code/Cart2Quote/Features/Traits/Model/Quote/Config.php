<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote;
use Cart2Quote\Quotation\Model\Quote\Status as QuoteStatus;
/**
 * Quote configuration model
 */
trait Config
{
    /**
     * Retrieve default status for state
     *
     * @param   string $state
     * @return  string
     */
    private function getStateDefaultStatus($state)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$status = false;
        $stateNode = $this->_getState($state);
        if ($stateNode) {
            $status = $this->quoteStatusFactory->create()->loadDefaultByState($state);
            $status = $status->getStatus();
        }
        return $status;
		}
	}
    /**
     * Get state object by state code
     *
     * @param string $state
     * @return Status|null
     */
    private function _getState($state)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			foreach ($this->_getCollection() as $item) {
            if ($item->getData('state') == $state) {
                return $item;
            }
        }
        return null;
		}
	}
    /**
     * Get collection
     *
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\Collection
     */
    private function _getCollection()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->collection == null) {
            $this->collection = $this->quoteStatusCollectionFactory->create()->joinStates();
        }
        return $this->collection;
		}
	}
    /**
     * Retrieve status label
     *
     * @param   string $code
     * @return  string
     */
    private function getStatusLabel($code)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$code = $this->maskStatusForArea($this->state->getAreaCode(), $code);
        $status = $this->quoteStatusFactory->create()->load($code);
        return $status->getStoreLabel();
		}
	}
    /**
     * Mask status for quote for specified area
     *
     * @param string $area
     * @param string $code
     * @return string
     */
    private function maskStatusForArea($area, $code)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (isset($this->maskStatusesMapping[$area][$code])) {
            return $this->maskStatusesMapping[$area][$code];
        }
        return $code;
		}
	}
    /**
     * State label getter
     *
     * @param string $state
     * @return \Magento\Framework\Phrase|string
     */
    private function getStateLabel($state)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($stateItem = $this->_getState($state)) {
            $label = $stateItem->getData('label');
            return __($label);
        }
        return $state;
		}
	}
    /**
     * Retrieve all statuses
     *
     * @return array
     */
    private function getStatuses()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$statuses = $this->quoteStatusCollectionFactory->create()->toOptionHash();
        return $statuses;
		}
	}
    /**
     * Get existing quote statuses
     * - Visible or invisible on frontend according to passed param
     *
     * @param bool $visibility
     * @return array
     */
    private function _getStatuses($visibility)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->statuses == null) {
            //define arrays
            $this->statuses[(bool)true] = [];
            $this->statuses[(bool)false] = [];
            foreach ($this->_getCollection() as $item) {
                $visible = $item->getData('visible_on_front');
                $this->statuses[(bool)$visible][] = $item->getData('status');
            }
        }
        return $this->statuses[(bool)$visibility];
		}
	}
    /**
     * Quote states getter
     *
     * @return array
     */
    private function getStates()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$states = [];
        foreach ($this->_getCollection() as $item) {
            if ($item->getState()) {
                $states[$item->getState()] = __($item->getData('label'));
            }
        }
        return $states;
		}
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
    private function getStateStatuses($state, $addLabels = true)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$key = (string)$state . '_' . (int)$addLabels;
        if (isset($this->stateStatuses[$key])) {
            return $this->stateStatuses[$key];
        }
        $statuses = [];
        if (!is_array($state)) {
            $state = [$state];
        }
        foreach ($state as $_state) {
            $stateNode = $this->_getState($_state);
            if ($stateNode) {
                $collection = $this->quoteStatusCollectionFactory->create()->addStateFilter($_state)->orderByLabel();
                foreach ($collection as $item) {
                    $status = $item->getData('status');
                    if ($addLabels) {
                        $statuses[$status] = $item->getStoreLabel();
                    } else {
                        $statuses[] = $status;
                    }
                }
            }
        }
        $this->stateStatuses[$key] = $statuses;
        return $statuses;
		}
	}
    /**
     * Retrieve states which are visible on front end
     *
     * @return array
     */
    private function getVisibleOnFrontStatuses()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_getStatuses(true);
		}
	}
    /**
     * Get quote statuses, invisible on frontend
     *
     * @return array
     */
    private function getInvisibleOnFrontStatuses()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_getStatuses(false);
		}
	}
}
