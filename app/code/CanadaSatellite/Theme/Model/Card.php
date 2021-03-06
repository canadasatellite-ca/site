<?php

namespace CanadaSatellite\Theme\Model;

class Card extends \Magento\Framework\Model\AbstractModel
{
	const CARD_GRID_INDEXER_ID = 'card_grid';

    /**
     * Cache tag
     */
    const CACHE_TAG = 'card_block';

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('CanadaSatellite\Theme\Model\ResourceModel\Card');
    }

    function getId()
    {
    	return $this->_data['new_sundriesid'];
    }

    function getLastFourDigits()
    {
    	$cardNumber = trim($this->_data['new_name']);
    	return substr($cardNumber, -4);
    }

    function getCardholderName()
    {
        if (!isset($this->_data['new_cardholdername'])) {
            return '';
        }
        return $this->_data['new_cardholdername'];
    }

    function getCardType()
    {
        if (!isset($this->_data['new_cardtype'])) {
            return '';
        }

        return $this->_data['new_cardtype'];
    }

    function getCardNumber()
    {
        return $this->_data['new_name'];
    }

    function getCardTypeLabel()
    {
        if (!isset($this->_data['new_cardtype@OData.Community.Display.V1.FormattedValue'])) {
            return '';
        }
        
        return $this->_data['new_cardtype@OData.Community.Display.V1.FormattedValue'];
    }

    function belongsToCustomer($customerId)
    {
        if (empty($customerId)) {
            return false;
        }

        if (!isset($this->_data['new_account']) || !isset($this->_data['new_account']->accountnumber)) {
            return false;
        }

        return $this->_data['new_account']->accountnumber == $customerId;
    }
}