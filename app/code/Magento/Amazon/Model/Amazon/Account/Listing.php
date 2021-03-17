<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Account;

use Magento\Amazon\Api\Data\AccountListingInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Listing
 */
class Listing extends AbstractModel implements AccountListingInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Account\Listing::class
        );
    }

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Get Amazon merchant id
     *
     * @return int|null
     */
    public function getMerchantId()
    {
        return $this->getData('merchant_id');
    }

    /**
     * Set Amazon merchant id
     *
     * @param int $id
     * @return $this
     */
    public function setMerchantId($id)
    {
        return $this->setData('merchant_id', $id);
    }

    /**
     * Get Amazon account third party is active flag
     *
     * @return int|null
     */
    public function getThirdpartyIsActive()
    {
        return $this->getData('thirdparty_is_active');
    }

    /**
     * Set Amazon account third party is active flag
     *
     * @param int $thirdpartyIsActive
     * @return $this
     */
    public function setThirdpartyIsActive($thirdpartyIsActive)
    {
        return $this->setData('thirdparty_is_active', $thirdpartyIsActive);
    }

    /**
     * Get Amazon account third party sku attribute field
     *
     * @return string|null
     */
    public function getThirdpartySkuField()
    {
        return $this->getData('thirdparty_sku_field');
    }

    /**
     * Set Amazon account third party sku attribute field
     *
     * @param string $thirdpartySkuField
     * @return $this
     */
    public function setThirdpartySkuField($thirdpartySkuField)
    {
        return $this->setData('thirdparty_sku_field', $thirdpartySkuField);
    }

    /**
     * Get Amazon account third party asin attribute field
     *
     * @return string|null
     */
    public function getThirdpartyAsinField()
    {
        return $this->getData('thirdparty_asin_field');
    }

    /**
     * Set Amazon account third party asin attribute field
     *
     * @param string $thirdpartyAsinField
     * @return $this
     */
    public function setThirdpartyAsinField($thirdpartyAsinField)
    {
        return $this->setData('thirdparty_asin_field', $thirdpartyAsinField);
    }

    /**
     * Get Amazon account auto list setting
     *
     * @return bool|null
     */
    public function getAutoList()
    {
        return $this->getData('auto_list');
    }

    /**
     * Set Amazon account auto list setting
     *
     * @param bool $autoList
     * @return $this
     */
    public function setAutoList($autoList)
    {
        return $this->setData('auto_list', $autoList);
    }

    /**
     * Get Amazon account handling time
     *
     * @return int|null
     */
    public function getHandlingTime()
    {
        return $this->getData('handling_time');
    }

    /**
     * Set Amazon account handling time
     *
     * @param int $handlingTime
     * @return $this
     */
    public function setHandlingTime($handlingTime)
    {
        return $this->setData('handling_time', $handlingTime);
    }

    /**
     * Get Amazon account listing condition
     *
     * @return int|null
     */
    public function getListCondition()
    {
        return $this->getData('list_condition');
    }

    /**
     * Set Amazon account listing condition
     *
     * @param int $listCondition
     * @return $this
     */
    public function setListCondition($listCondition)
    {
        return $this->setData('list_condition', $listCondition);
    }

    /**
     * Get Amazon account seller notes
     *
     * @return string|null
     */
    public function getSellerNotes()
    {
        return $this->getData('seller_notes');
    }

    /**
     * Set Amazon account seller notes
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotes($notes)
    {
        return $this->setData('seller_notes', $notes);
    }

    /**
     * Get Amazon account listing condition attribute field
     *
     * @return string|null
     */
    public function getListConditionField()
    {
        return $this->getData('list_condition_field');
    }

    /**
     * Set Amazon account listing condition attribute field
     *
     * @param string $listConditionField
     * @return $this
     */
    public function setListConditionField($listConditionField)
    {
        return $this->setData('list_condition_field', $listConditionField);
    }

    /**
     * Get Amazon account listing condition new
     *
     * @return string|null
     */
    public function getListConditionNew()
    {
        return $this->getData('list_condition_new');
    }

    /**
     * Set Amazon account listing condition new attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionNew($value)
    {
        return $this->setData('list_condition_new', $value);
    }

    /**
     * Get Amazon account listing condition refurbished attribute value
     *
     * @return string|null
     */
    public function getListConditionRefurbished()
    {
        return $this->getData('list_condition_refurbished');
    }

    /**
     * Set Amazon account listing condition refurbished attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionRefurbished($value)
    {
        return $this->setData('list_condition_refurbished', $value);
    }

    /**
     * Get Amazon account seller notes refurbished
     *
     * @return string|null
     */
    public function getSellerNotesRefurbished()
    {
        return $this->getData('seller_notes_refurbished');
    }

    /**
     * Set Amazon account seller notes refurbished
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesRefurbished($notes)
    {
        return $this->setData('seller_notes_refurbished', $notes);
    }

    /**
     * Get Amazon account listing condition like new attribute value
     *
     * @return string|null
     */
    public function getListConditionLikenew()
    {
        return $this->getData('list_condition_likenew');
    }

    /**
     * Set Amazon account listing condition like new attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionLikenew($value)
    {
        return $this->setData('list_condition_likenew', $value);
    }

    /**
     * Get Amazon account seller notes like new
     *
     * @return string|null
     */
    public function getSellerNotesLikenew()
    {
        return $this->getData('seller_notes_likenew');
    }

    /**
     * Set Amazon account seller notes like new
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesLikenew($notes)
    {
        return $this->setData('seller_notes_likenew', $notes);
    }

    /**
     * Get Amazon account listing condition very good attribute value
     *
     * @return string|null
     */
    public function getListConditionVerygood()
    {
        return $this->getData('list_condition_verygood');
    }

    /**
     * Set Amazon account listing condition very good attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionVerygood($value)
    {
        return $this->setData('list_condition_verygood', $value);
    }

    /**
     * Get Amazon account seller notes very good
     *
     * @return string|null
     */
    public function getSellerNotesVerygood()
    {
        return $this->getData('seller_notes_verygood');
    }

    /**
     * Set Amazon account seller notes very good
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesVerygood($notes)
    {
        return $this->setData('seller_notes_verygood', $notes);
    }

    /**
     * Get Amazon account listing condition good attribute value
     *
     * @return string|null
     */
    public function getListConditionGood()
    {
        return $this->getData('list_condition_good');
    }

    /**
     * Set Amazon account listing condition good attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionGood($value)
    {
        return $this->setData('list_condition_good', $value);
    }

    /**
     * Get Amazon account seller notes good
     *
     * @return string|null
     */
    public function getSellerNotesGood()
    {
        return $this->getData('seller_notes_good');
    }

    /**
     * Set Amazon account seller notes good
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesGood($notes)
    {
        return $this->setData('seller_notes_good', $notes);
    }

    /**
     * Get Amazon account listing condition acceptable attribute value
     *
     * @return string|null
     */
    public function getListConditionAcceptable()
    {
        return $this->getData('list_condition_acceptable');
    }

    /**
     * Set Amazon account listing condition acceptable attribute value
     *
     * @param string $value
     *
     * @return $this
     */
    public function setListConditionAcceptable($value)
    {
        return $this->setData('list_condition_acceptable', $value);
    }

    /**
     * Get Amazon account seller notes acceptable
     *
     * @return string|null
     */
    public function getSellerNotesAcceptable()
    {
        return $this->getData('seller_notes_acceptable');
    }

    /**
     * Set Amazon account seller notes acceptable
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesAcceptable($notes)
    {
        return $this->setData('seller_notes_acceptable', $notes);
    }

    /**
     * Get Amazon account listing condition collectible like new attribute value
     *
     * @return string|null
     */
    public function getListConditionCollectibleLikenew()
    {
        return $this->getData('list_condition_collectible_likenew');
    }

    /**
     * Set Amazon account listing condition collectible like new attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionCollectibleLikenew($value)
    {
        return $this->setData('list_condition_collectible_likenew', $value);
    }

    /**
     * Get Amazon account seller notes collectible like new
     *
     * @return string|null
     */
    public function getSellerNotesCollectibleLikenew()
    {
        return $this->getData('seller_notes_collectible_likenew');
    }

    /**
     * Set Amazon account seller notes collectible like new
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesCollectibleLikenew($notes)
    {
        return $this->setData('seller_notes_collectible_likenew', $notes);
    }

    /**
     * Get Amazon account listing condition collectible very good attribute value
     *
     * @return string|null
     */
    public function getListConditionCollectibleVerygood()
    {
        return $this->getData('list_condition_collectible_verygood');
    }

    /**
     * Set Amazon account listing condition collectible very good attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionCollectibleVerygood($value)
    {
        return $this->setData('list_condition_collectible_verygood', $value);
    }

    /**
     * Get Amazon account seller notes collectible very good
     *
     * @return string|null
     */
    public function getSellerNotesCollectibleVerygood()
    {
        return $this->getData('seller_notes_collectible_verygood');
    }

    /**
     * Set Amazon account seller notes collectible very good
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesCollectibleVerygood($notes)
    {
        return $this->setData('seller_notes_collectible_verygood', $notes);
    }

    /**
     * Get Amazon account listing condition collectible good attribute value
     *
     * @return string|null
     */
    public function getListConditionCollectibleGood()
    {
        return $this->getData('list_condition_collectible_good');
    }

    /**
     * Set Amazon account listing condition collectible good attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionCollectibleGood($value)
    {
        return $this->setData('list_condition_collectible_good', $value);
    }

    /**
     * Get Amazon account seller notes collectible good
     *
     * @return string|null
     */
    public function getSellerNotesCollectibleGood()
    {
        return $this->getData('seller_notes_collectible_good');
    }

    /**
     * Set Amazon account seller notes collectible good
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesCollectibleGood($notes)
    {
        return $this->setData('seller_notes_collectible_good', $notes);
    }

    /**
     * Get Amazon account listing condition collectible acceptable attribute value
     *
     * @return string|null
     */
    public function getListConditionCollectibleAcceptable()
    {
        return $this->getData('list_condition_collectible_acceptable');
    }

    /**
     * Set Amazon account listing condition collectible acceptable attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionCollectibleAcceptable($value)
    {
        return $this->setData('list_condition_collectible_acceptable', $value);
    }

    /**
     * Get Amazon account seller notes collectible acceptable
     *
     * @return string|null
     */
    public function getSellerNotesCollectibleAcceptable()
    {
        return $this->getData('seller_notes_collectible_acceptable');
    }

    /**
     * Set Amazon account seller notes collectible acceptable
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesCollectibleAcceptable($notes)
    {
        return $this->setData('seller_notes_collectible_acceptable', $notes);
    }

    /**
     * Get Amazon account asin mapping attribute field
     *
     * @return string|null
     */
    public function getAsinMappingField()
    {
        return $this->getData('asin_mapping_field');
    }

    /**
     * Set Amazon account asin mapping field
     *
     * @param string $value
     * @return $this
     */
    public function setAsinMappingField($value)
    {
        return $this->setData('asin_mapping_field', $value);
    }

    /**
     * Get Amazon account ean mapping attribute field
     *
     * @return string|null
     */
    public function getEanMappingField()
    {
        return $this->getData('ean_mapping_field');
    }

    /**
     * Set Amazon account ean mapping attribute field
     *
     * @param string $value
     * @return $this
     */
    public function setEanMappingField($value)
    {
        return $this->setData('ean_mapping_field', $value);
    }

    /**
     * Get Amazon account gcid mapping attribute field
     *
     * @return string|null
     */
    public function getGcidMappingField()
    {
        return $this->getData('gcid_mapping_field');
    }

    /**
     * Set Amazon account gcid mapping attribute field
     *
     * @param string $value
     * @return $this
     */
    public function setGcidMappingField($value)
    {
        return $this->setData('gcid_mapping_field', $value);
    }

    /**
     * Get Amazon account isbn mapping attribute field
     *
     * @return string|null
     */
    public function getIsbnMappingField()
    {
        return $this->getData('isbn_mapping_field');
    }

    /**
     * Set Amazon account isbn mapping attribute field
     *
     * @param string $value
     * @return $this
     */
    public function setIsbnMappingField($value)
    {
        return $this->setData('isbn_mapping_field', $value);
    }

    /**
     * Get Amazon account upc mapping attribute field
     *
     * @return string|null
     */
    public function getUpcMappingField()
    {
        return $this->getData('upc_mapping_field');
    }

    /**
     * Set Amazon account upc mapping attribute field
     *
     * @param string $value
     * @return $this
     */
    public function setUpcMappingField($value)
    {
        return $this->setData('upc_mapping_field', $value);
    }

    /**
     * Get Amazon account general mapping attribute field
     *
     * @return string|null
     */
    public function getGeneralMappingField()
    {
        return $this->getData('general_mapping_field');
    }

    /**
     * Set Amazon account general mapping attribute field
     *
     * @param string $value
     * @return $this
     */
    public function setGeneralMappingField($value)
    {
        return $this->setData('general_mapping_field', $value);
    }

    /**
     * Get Amazon account fulfilled by
     *
     * @return int|null
     */
    public function getFulfilledBy()
    {
        return $this->getData('fulfilled_by');
    }

    /**
     * Set Amazon account fulfilled by
     *
     * @param int $value
     * @return $this
     */
    public function setFulfilledBy($value)
    {
        return $this->setData('fulfilled_by', $value);
    }

    /**
     * Get Amazon account fulfillment by attribute field
     *
     * @return string|null
     */
    public function getFulfilledByField()
    {
        return $this->getData('fulfilled_by_field');
    }

    /**
     * Set Amazon account fulfillment by attribute field
     *
     * @param string $value
     * @return $this
     */
    public function setFulfilledByField($value)
    {
        return $this->setData('fufilled_by_field', $value);
    }

    /**
     * Get Amazon account fulfilled by seller attribute value
     *
     * @return string|null
     */
    public function getFulfilledBySeller()
    {
        return $this->getData('fulfilled_by_seller');
    }

    /**
     * Set Amazon account fulfilled by seller attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setFulfilledBySeller($value)
    {
        return $this->setData('fulfilled_by_seller', $value);
    }

    /**
     * Get Amazon account fulfilled by amazon attribute value
     *
     * @return string|null
     */
    public function getFulfilledByAmazon()
    {
        return $this->getData('fulfilled_by_amazon');
    }

    /**
     * Set Amazon account fulfilled by amazon attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setFulfilledByAmazon($value)
    {
        return $this->setData('fulfilled_by_amazon', $value);
    }

    /**
     * Get Amazon account listing quantity custom
     *
     * @return int|null
     */
    public function getCustomQty()
    {
        return $this->getData('custom_qty');
    }

    /**
     * Set Amazon account listing quantity custom
     *
     * @param $customQty
     * @return $this
     */
    public function setCustomQty($customQty)
    {
        return $this->setData('custom_qty', $customQty);
    }

    /**
     * Get Amazon account minimum quantity
     *
     * @return int|null
     */
    public function getMinQty()
    {
        return $this->getData('min_qty');
    }

    /**
     * Set Amazon account minimum quantity
     *
     * @param int $value
     * @return $this
     */
    public function setMinQty($value)
    {
        return $this->setData('min_qty', $value);
    }

    /**
     * Get Amazon account maximum quantity
     *
     * @return int|null
     */
    public function getMaxQty()
    {
        return $this->getData('max_qty');
    }

    /**
     * Set Amazon account maximum quantity
     *
     * @param string $value
     * @return $this
     */
    public function setMaxQty($value)
    {
        return $this->setData('max_qty', $value);
    }

    /**
     * Get Amazon account price source
     *
     * @return string|null
     */
    public function getPriceField()
    {
        return $this->getData('price_field');
    }

    /**
     * Set Amazon account price source
     *
     * @param string $value
     * @return $this
     */
    public function setPriceField($value)
    {
        return $this->setData('price_field', $value);
    }

    /**
     * Get Amazon account strike price field
     *
     * @return string|null
     */
    public function getStrikePriceField()
    {
        return $this->getData('strike_price_field');
    }

    /**
     * Set Amazon account strike price field
     *
     * @param string $value
     * @return $this
     */
    public function setStrikePriceField($value)
    {
        return $this->setData('strike_price_field', $value);
    }

    /**
     * Get Amazon account map price field
     *
     * @return string|null
     */
    public function getMapPriceField()
    {
        return $this->getData('map_price_field');
    }

    /**
     * Set Amazon account map price field
     *
     * @param string $value
     * @return $this
     */
    public function setMapPriceField($value)
    {
        return $this->setData('map_price_field', $value);
    }

    /**
     * Get Amazon account vat flag
     *
     * @return int|null
     */
    public function getVatIsActive()
    {
        return $this->getData('vat_is_active');
    }

    /**
     * Set Amazon account vat flag
     *
     * @param int $value
     * @return $this
     */
    public function setVatIsActive($value)
    {
        return $this->setData('vat_is_active', $value);
    }

    /**
     * Get Amazon account vat percentage
     *
     * @return float|null
     */
    public function getVatPercentage()
    {
        return $this->getData('vat_percentage');
    }

    /**
     * Set Amazon account vat percentage
     *
     * @param float $value
     * @return $this
     */
    public function setVatPercentage($value)
    {
        return $this->setData('vat_percentage', $value);
    }

    /**
     * Get Amazon account currency conversion flag
     *
     * @return int|null
     */
    public function getCcIsActive()
    {
        return $this->getData('cc_is_active');
    }

    /**
     * Set Amazon account currency conversion flag
     *
     * @param int $value
     * @return $this
     */
    public function setCcIsActive($value)
    {
        return $this->setData('cc_is_active', $value);
    }

    /**
     * Get Amazon account currency conversion rate
     *
     * @return float|null
     */
    public function getCcRate()
    {
        return $this->getData('cc_rate');
    }

    /**
     * Set Amazon account currency conversion rate
     *
     * @param float $value
     * @return $this
     */
    public function setCcRate($value)
    {
        return $this->setData('cc_rate', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getManagePtc()
    {
        return $this->getData('manage_ptc');
    }

    /**
     * {@inheritdoc}
     */
    public function setManagePtc(bool $value)
    {
        $this->setData('manage_ptc', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultPtc()
    {
        return $this->getData('default_ptc');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultPtc(string $value)
    {
        $this->setData('default_ptc', $value);
    }
}
