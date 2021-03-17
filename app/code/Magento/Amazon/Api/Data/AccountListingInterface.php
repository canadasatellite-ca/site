<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api\Data;

/**
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface AccountListingInterface
{
    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get merchant id
     *
     * @return int|null
     */
    public function getMerchantId();

    /**
     * Set merchant id
     *
     * @param int $id
     * @return $this
     */
    public function setMerchantId($id);

    /**
     * Get Amazon account third party is active flag
     *
     * @return int|null
     */
    public function getThirdpartyIsActive();

    /**
     * Set Amazon account third party is active flag
     *
     * @param int $thirdpartyIsActive
     * @return $this
     */
    public function setThirdpartyIsActive($thirdpartyIsActive);

    /**
     * Get Amazon account third party sku attribute field
     *
     * @return string|null
     */
    public function getThirdpartySkuField();

    /**
     * Set Amazon account third party sku attribute field
     *
     * @param string $thirdpartySkuField
     * @return $this
     */
    public function setThirdpartySkuField($thirdpartySkuField);

    /**
     * Get Amazon account third party asin attribute field
     *
     * @return string|null
     */
    public function getThirdpartyAsinField();

    /**
     * Set Amazon account third party asin attribute field
     *
     * @param string $thirdpartyAsinField
     * @return $this
     */
    public function setThirdpartyAsinField($thirdpartyAsinField);

    /**
     * Get Amazon account auto list setting
     *
     * @return bool|null
     */
    public function getAutoList();

    /**
     * Set Amazon account auto list setting
     *
     * @param bool $autoList
     * @return $this
     */
    public function setAutoList($autoList);

    /**
     * Get Amazon account handling time
     *
     * @return int|null
     */
    public function getHandlingTime();

    /**
     * Set Amazon account handling time
     *
     * @param int $handlingTime
     * @return $this
     */
    public function setHandlingTime($handlingTime);

    /**
     * Get Amazon account listing condition
     *
     * @return int|null
     */
    public function getListCondition();

    /**
     * Set Amazon account listing condition
     *
     * @param int $listCondition
     * @return $this
     */
    public function setListCondition($listCondition);

    /**
     * Get Amazon account seller notes
     *
     * @return string|null
     */
    public function getSellerNotes();

    /**
     * Set Amazon account seller notes
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotes($notes);

    /**
     * Get Amazon account listing condition attribute field
     *
     * @return string|null
     */
    public function getListConditionField();

    /**
     * Set Amazon account listing condition attribute field
     *
     * @param string $listConditionAttribute
     * @return $this
     */
    public function setListConditionField($listConditionAttribute);

    /**
     * Get Amazon account listing condition new
     *
     * @return string|null
     */
    public function getListConditionNew();

    /**
     * Set Amazon account listing condition new attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionNew($value);

    /**
     * Get Amazon account listing condition refurbished attribute value
     *
     * @return string|null
     */
    public function getListConditionRefurbished();

    /**
     * Set Amazon account listing condition refurbished attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionRefurbished($value);

    /**
     * Get Amazon account seller notes refurbished
     *
     * @return string|null
     */
    public function getSellerNotesRefurbished();

    /**
     * Set Amazon account seller notes refurbished
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesRefurbished($notes);

    /**
     * Get Amazon account listing condition like new attribute value
     *
     * @return string|null
     */
    public function getListConditionLikenew();

    /**
     * Set Amazon account listing condition like new attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionLikenew($value);

    /**
     * Get Amazon account seller notes like new
     *
     * @return string|null
     */
    public function getSellerNotesLikenew();

    /**
     * Set Amazon account seller notes like new
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesLikenew($notes);

    /**
     * Get Amazon account listing condition very good attribute value
     *
     * @return string|null
     */
    public function getListConditionVerygood();

    /**
     * Set Amazon account listing condition very good attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionVerygood($value);

    /**
     * Get Amazon account seller notes very good
     *
     * @return string|null
     */
    public function getSellerNotesVerygood();

    /**
     * Set Amazon account seller notes very good
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesVerygood($notes);

    /**
     * Get Amazon account listing condition good attribute value
     *
     * @return string|null
     */
    public function getListConditionGood();

    /**
     * Set Amazon account listing condition good attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionGood($value);

    /**
     * Get Amazon account seller notes good
     *
     * @return string|null
     */
    public function getSellerNotesGood();

    /**
     * Set Amazon account seller notes good
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesGood($notes);

    /**
     * Get Amazon account listing condition acceptable attribute value
     *
     * @return string|null
     */
    public function getListConditionAcceptable();

    /**
     * Set Amazon account listing condition acceptable attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionAcceptable($value);

    /**
     * Get Amazon account seller notes acceptable
     *
     * @return string|null
     */
    public function getSellerNotesAcceptable();

    /**
     * Set Amazon account seller notes acceptable
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesAcceptable($notes);

    /**
     * Get Amazon account listing condition collectible like new attribute value
     *
     * @return string|null
     */
    public function getListConditionCollectibleLikenew();

    /**
     * Set Amazon account listing condition collectible like new attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionCollectibleLikenew($value);

    /**
     * Get Amazon account seller notes collectible like new
     *
     * @return string|null
     */
    public function getSellerNotesCollectibleLikenew();

    /**
     * Set Amazon account seller notes collectible like new
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesCollectibleLikenew($notes);

    /**
     * Get Amazon account listing condition collectible very good attribute value
     *
     * @return string|null
     */
    public function getListConditionCollectibleVerygood();

    /**
     * Set Amazon account listing condition collectible very good attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionCollectibleVerygood($value);

    /**
     * Get Amazon account seller notes collectible very good
     *
     * @return string|null
     */
    public function getSellerNotesCollectibleVerygood();

    /**
     * Set Amazon account seller notes collectible very good
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesCollectibleVerygood($notes);

    /**
     * Get Amazon account listing condition collectible good attribute value
     *
     * @return string|null
     */
    public function getListConditionCollectibleGood();

    /**
     * Set Amazon account listing condition collectible good attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionCollectibleGood($value);

    /**
     * Get Amazon account seller notes collectible good
     *
     * @return string|null
     */
    public function getSellerNotesCollectibleGood();

    /**
     * Set Amazon account seller notes collectible good
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesCollectibleGood($notes);

    /**
     * Get Amazon account listing condition collectible acceptable attribute value
     *
     * @return string|null
     */
    public function getListConditionCollectibleAcceptable();

    /**
     * Set Amazon account listing condition collectible acceptable attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setListConditionCollectibleAcceptable($value);

    /**
     * Get Amazon account seller notes collectible acceptable
     *
     * @return string|null
     */
    public function getSellerNotesCollectibleAcceptable();

    /**
     * Set Amazon account seller notes collectible acceptable
     *
     * @param string $notes
     * @return $this
     */
    public function setSellerNotesCollectibleAcceptable($notes);

    /**
     * Get Amazon account asin mapping attribute field
     *
     * @return string|null
     */
    public function getAsinMappingField();

    /**
     * Set Amazon account asin mapping field
     *
     * @param string $value
     * @return $this
     */
    public function setAsinMappingField($value);

    /**
     * Get Amazon account ean mapping attribute field
     *
     * @return string|null
     */
    public function getEanMappingField();

    /**
     * Set Amazon account ean mapping attribute field
     *
     * @param string $value
     * @return $this
     */
    public function setEanMappingField($value);

    /**
     * Get Amazon account gcid mapping attribute field
     *
     * @return string|null
     */
    public function getGcidMappingField();

    /**
     * Set Amazon account gcid mapping attribute field
     *
     * @param string $value
     * @return $this
     */
    public function setGcidMappingField($value);

    /**
     * Get Amazon account isbn mapping attribute field
     *
     * @return string|null
     */
    public function getIsbnMappingField();

    /**
     * Set Amazon account isbn mapping attribute field
     *
     * @param string $value
     * @return $this
     */
    public function setIsbnMappingField($value);

    /**
     * Get Amazon account upc mapping attribute field
     *
     * @return string|null
     */
    public function getUpcMappingField();

    /**
     * Set Amazon account upc mapping attribute field
     *
     * @param string $value
     * @return $this
     */
    public function setUpcMappingField($value);

    /**
     * Get Amazon account general mapping attribute field
     *
     * @return string|null
     */
    public function getGeneralMappingField();

    /**
     * Set Amazon account general mapping attribute field
     *
     * @param string $value
     * @return $this
     */
    public function setGeneralMappingField($value);

    /**
     * Get Amazon account fulfillment by
     *
     * @return int|null
     */
    public function getFulfilledBy();

    /**
     * Set Amazon account fulfillment by
     *
     * @param int $value
     * @return $this
     */
    public function setFulfilledBy($value);

    /**
     * Get Amazon account fulfillment by attribute field
     *
     * @return string|null
     */
    public function getFulfilledByField();

    /**
     * Set Amazon account fulfillment by attribute field
     *
     * @param string $value
     * @return $this
     */
    public function setFulfilledByField($value);

    /**
     * Get Amazon account fulfilled by seller attribute value
     *
     * @return string|null
     */
    public function getFulfilledBySeller();

    /**
     * Set Amazon account fulfilled by seller attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setFulfilledBySeller($value);

    /**
     * Get Amazon account fulfilled by amazon attribute value
     *
     * @return string|null
     */
    public function getFulfilledByAmazon();

    /**
     * Set Amazon account fulfilled by amazon attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setFulfilledByAmazon($value);

    /**
     * Get Amazon account listing quantity custom
     *
     * @return int|null
     */
    public function getCustomQty();

    /**
     * Set Amazon account listing quantity custom
     *
     * @param $customQty
     * @return $this
     *
     */
    public function setCustomQty($customQty);

    /**
     * Get Amazon account minimum quantity
     *
     * @return int|null
     */
    public function getMinQty();

    /**
     * Set Amazon account minimum quantity
     *
     * @param int $value
     * @return $this
     */
    public function setMinQty($value);

    /**
     * Get Amazon account maximum quantity
     *
     * @return int|null
     */
    public function getMaxQty();

    /**
     * Set Amazon account maximum quantity
     *
     * @param string $value
     * @return $this
     */
    public function setMaxQty($value);

    /**
     * Get Amazon account price source
     *
     * @return string|null
     */
    public function getPriceField();

    /**
     * Set Amazon account price source
     *
     * @param string $value
     * @return $this
     */
    public function setPriceField($value);

    /**
     * Get Amazon account strike price field
     *
     * @return string|null
     */
    public function getStrikePriceField();

    /**
     * Set Amazon account strike price field
     *
     * @param string $value
     * @return $this
     */
    public function setStrikePriceField($value);

    /**
     * Get Amazon account map price field
     *
     * @return string|null
     */
    public function getMapPriceField();

    /**
     * Set Amazon account map price field
     *
     * @param string $value
     * @return $this
     */
    public function setMapPriceField($value);

    /**
     * Get Amazon account vat flag
     *
     * @return int|null
     */
    public function getVatIsActive();

    /**
     * Set Amazon account vat flag
     *
     * @param int $value
     * @return $this
     */
    public function setVatIsActive($value);

    /**
     * Get Amazon account vat percentage
     *
     * @return float|null
     */
    public function getVatPercentage();

    /**
     * Set Amazon account vat percentage
     *
     * @param float $value
     * @return $this
     */
    public function setVatPercentage($value);

    /**
     * Get Amazon account currency conversion flag
     *
     * @return int|null
     */
    public function getCcIsActive();

    /**
     * Set Amazon account currency conversion flag
     *
     * @param int $value
     * @return $this
     */
    public function setCcIsActive($value);

    /**
     * Get Amazon account currency conversion rate
     *
     * @return float|null
     */
    public function getCcRate();

    /**
     * Set Amazon account currency conversion rate
     *
     * @param float $value
     * @return $this
     */
    public function setCcRate($value);

    /**
     * @return bool|null
     */
    public function getManagePtc();

    /**
     * @param bool $value
     * @return void
     */
    public function setManagePtc(bool $value);

    /**
     * @return string|null
     */
    public function getDefaultPtc();

    /**
     * @param string $value
     * @return void
     */
    public function setDefaultPtc(string $value);
}
