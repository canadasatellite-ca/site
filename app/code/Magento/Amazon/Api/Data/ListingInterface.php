<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api\Data;

/**
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface ListingInterface
{
    /**
     * Get listing id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get listing merchant id
     *
     * @return int|null
     */
    public function getMerchantId();

    /**
     * Set listing merchant id
     *
     * @param int $merchantId
     * @return $this
     */
    public function setMerchantId($merchantId);

    /**
     * Get listing amazon id
     *
     * @return string|null
     */
    public function getListingId();

    /**
     * Set listing amazon id
     *
     * @param string $listingId
     * @return $this
     */
    public function setListingId($listingId);

    /**
     * Get listing catalog product id
     *
     * @return string|null
     */
    public function getCatalogProductId();

    /**
     * Set listing catalog product id
     *
     * @param string $id
     * @return $this
     */
    public function setCatalogProductId($id);

    /**
     * Get listing product id type
     *
     * @return int|null
     */
    public function getProductIdType();

    /**
     * Set listing product id type
     *
     * @param int $type
     * @return $this
     */
    public function setProductIdType($type);

    /**
     * Get listing product id
     *
     * @return string|null
     */
    public function getProductId();

    /**
     * Set listing product id
     *
     * @param string $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Get listing product type
     *
     * @return string|null
     */
    public function getProductType();

    /**
     * Set listing product type
     *
     * @param string $productType
     * @return $this
     */
    public function setProductType($productType);

    /**
     * Get listing category id
     *
     * @return string|null
     */
    public function getCategoryId();

    /**
     * Set listing category id
     *
     * @param string $categoryId
     * @return $this
     */
    public function setCategoryId($categoryId);

    /**
     * Get listing asin
     *
     * @return string|null
     */
    public function getAsin();

    /**
     * Set listing asin
     *
     * @param string $asin
     * @return $this
     */
    public function setAsin($asin);

    /**
     * Get listing catalog sku
     *
     * @return string|null
     */
    public function getCatalogSku();

    /**
     * Set listing catalog sku
     *
     * @param string $sku
     * @return $this
     */
    public function setCatalogSku($sku);

    /**
     * Get listing amazon sku
     *
     * @return string|null
     */
    public function getSellerSku();

    /**
     * Set listing seller sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSellerSku($sku);

    /**
     * Get listing amazon name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set listing amazon name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get listing amazon qty
     *
     * @return float|null
     */
    public function getQty();

    /**
     * Set listing amazon qty
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * Get listing amazon listing price
     *
     * @return float|null
     */
    public function getListPrice();

    /**
     * Set listing amazon listing price
     *
     * @param float $price
     * @return $this
     */
    public function setListPrice($price);

    /**
     * Get listing amazon shipping price
     *
     * @return float|null
     */
    public function getShippingPrice();

    /**
     * Set listing amazon shipping price
     *
     * @param float $price
     * @return $this
     */
    public function setShippingPrice($price);

    /**
     * Get listing amazon landed price
     *
     * @return float|null
     */
    public function getLandedPrice();

    /**
     * Set listing amazon landed price
     *
     * @param float $price
     * @return $this
     */
    public function setLandedPrice($price);

    /**
     * Get listing amazon MSRP price
     *
     * @return float|null
     */
    public function getMsrpPrice();

    /**
     * Set listing amazon MSRP price
     *
     * @param float $price
     * @return $this
     */
    public function setMsrpPrice($price);

    /**
     * Get listing amazon MAP price
     *
     * @return float|null
     */
    public function getMapPrice();

    /**
     * Set listing amazon MAP price
     *
     * @param float $price
     * @return $this
     */
    public function setMapPrice($price);

    /**
     * Get listing variants flag
     *
     * @return int|null
     */
    public function getVariants();

    /**
     * Set listing variants flag
     *
     * @param int $flag
     * @return $this
     */
    public function setVariants($flag);

    /**
     * Get listing condition
     *
     * @return int|null
     */
    public function getCondition();

    /**
     * Set listing condition
     *
     * @param int $condition
     * @return $this
     */
    public function setCondition($condition);

    /**
     * Get listing is listed flag
     *
     * @return int|null
     */
    public function getIsListed();

    /**
     * Set listing is listed flag
     *
     * @param int $flag
     * @return $this
     */
    public function setIsListed($flag);

    /**
     * Get listing eligibility flag
     *
     * @return int|null
     */
    public function getEligible();

    /**
     * Set listing eligiblity flag
     *
     * @param int $flag
     * @return $this
     */
    public function setEligible($flag);

    /**
     * Get listing fulfilled by
     *
     * @return string|null
     */
    public function getFulfilledBy();

    /**
     * Set listing fulfilled by
     *
     * @param string $value
     * @return $this
     */
    public function setFulfilledBy($value);

    /**
     * Get listing pricing update flag
     *
     * @return int|null
     */
    public function getPricingUpdate();

    /**
     * Set listing pricing update flag
     *
     * @param int $flag
     * @return $this
     */
    public function setPricingUpdate($flag);

    /**
     * Get listing quantity update flag
     *
     * @return int|null
     */
    public function getQuantityUpdate();

    /**
     * Set listing quantity update flag
     *
     * @param int $flag
     * @return $this
     */
    public function setQuantityUpdate($flag);

    /**
     * Get listing fulfilled by update flag
     *
     * @return int|null
     */
    public function getFulfilledByUpdate();

    /**
     * Set listing fulfilled by update flag
     *
     * @param int $flag
     * @return $this
     */
    public function setFulfilledByUpdate($flag);

    /**
     * Get listing shipping calculated flag
     *
     * @return int|null
     */
    public function getIsShip();

    /**
     * Set listing shipping calculated flag
     *
     * @param int $flag
     * @return $this
     */
    public function setIsShip($flag);

    /**
     * Get listing status
     *
     * @return int|null
     */
    public function getListStatus();

    /**
     * Set listing status
     *
     * @param int $status
     * @return $this
     */
    public function setListStatus($status);

    /**
     * Get listing condition override
     *
     * @return int|null
     */
    public function getConditionOverride();

    /**
     * Set listing condition override
     *
     * @param int $flag
     * @return $this
     */
    public function setConditionOverride($flag);

    /**
     * Get listing condition notes override
     *
     * @return string|null
     */
    public function getConditionNotesOverride();

    /**
     * Set listing condition notes override
     *
     * @param string $value
     * @return $this
     */
    public function setConditionNotesOverride($value);

    /**
     * Get listing amazon listing price override
     *
     * @return float|null
     */
    public function getListPriceOverride();

    /**
     * Set listing amazon listing price override
     *
     * @param float $price
     * @return $this
     */
    public function setListPriceOverride($price);

    /**
     * Get listing handling override
     *
     * @return int|null
     */
    public function getHandlingOverride();

    /**
     * Set listing handling override
     *
     * @param int $value
     * @return $this
     */
    public function setHandlingOverride($value);
}
