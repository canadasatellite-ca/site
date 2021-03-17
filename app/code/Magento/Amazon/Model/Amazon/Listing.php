<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon;

use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Listing
 *
 */
class Listing extends AbstractModel implements ListingInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Listing::class
        );
    }

    /**
     * Get listing id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Get listing merchant id
     *
     * @return int|null
     */
    public function getMerchantId()
    {
        return $this->getData('merchant_id');
    }

    /**
     * Set listing merchant id
     *
     * @param int $merchantId
     * @return $this
     */
    public function setMerchantId($merchantId)
    {
        return $this->setData('merchant_id', $merchantId);
    }

    /**
     * Get listing amazon id
     *
     * @return string|null
     */
    public function getListingId()
    {
        return $this->getData('listing_id');
    }

    /**
     * Set listing amazon id
     *
     * @param string $listingId
     * @return $this
     */
    public function setListingId($listingId)
    {
        return $this->setData('listing_id', $listingId);
    }

    /**
     * Get listing catalog product id
     *
     * @return string|null
     */
    public function getCatalogProductId()
    {
        return $this->getData('catalog_product_id');
    }

    /**
     * Set listing catalog product id
     *
     * @param string $id
     * @return $this
     */
    public function setCatalogProductId($id)
    {
        return $this->setData('catalog_product_id', $id);
    }

    /**
     * Get listing product id type
     *
     * @return int|null
     */
    public function getProductIdType()
    {
        return $this->getData('product_id_type');
    }

    /**
     * Set listing product id type
     *
     * @param int $type
     * @return $this
     */
    public function setProductIdType($type)
    {
        return $this->setData('product_id_type', $type);
    }

    /**
     * Get listing product id
     *
     * @return string|null
     */
    public function getProductId()
    {
        return $this->getData('product_id');
    }

    /**
     * Set listing product id
     *
     * @param string $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->setData('product_id', $productId);
    }

    /**
     * Get listing product type
     *
     * @return string|null
     */
    public function getProductType()
    {
        return $this->getData('product_type');
    }

    /**
     * Set listing product type
     *
     * @param string $productType
     * @return $this
     */
    public function setProductType($productType)
    {
        return $this->setData('product_type', $productType);
    }

    /**
     * Get listing category id
     *
     * @return string|null
     */
    public function getCategoryId()
    {
        return $this->getData('category_id');
    }

    /**
     * Set listing category id
     *
     * @param string $categoryId
     * @return $this
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData('category_id', $categoryId);
    }

    /**
     * Get listing asin
     *
     * @return string|null
     */
    public function getAsin()
    {
        return $this->getData('asin');
    }

    /**
     * Set listing asin
     *
     * @param string $asin
     * @return $this
     */
    public function setAsin($asin)
    {
        return $this->setData('asin', $asin);
    }

    /**
     * Get listing catalog sku
     *
     * @return string|null
     */
    public function getCatalogSku()
    {
        return $this->getData('catalog_sku');
    }

    /**
     * Set listing catalog sku
     *
     * @param string $sku
     * @return $this
     */
    public function setCatalogSku($sku)
    {
        return $this->setData('catalog_sku', $sku);
    }

    /**
     * Get listing amazon sku
     *
     * @return string|null
     */
    public function getSellerSku()
    {
        return $this->getData('seller_sku');
    }

    /**
     * Set listing seller sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSellerSku($sku)
    {
        return $this->setData('seller_sku', $sku);
    }

    /**
     * Get listing amazon name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * Set listing amazon name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData('name', $name);
    }

    /**
     * Get listing amazon qty
     *
     * @return float|null
     */
    public function getQty()
    {
        return $this->getData('qty');
    }

    /**
     * Set listing amazon qty
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty)
    {
        return $this->setData('qty', $qty);
    }

    /**
     * Get listing amazon listing price
     *
     * @return float|null
     */
    public function getListPrice()
    {
        return $this->getData('list_price');
    }

    /**
     * Set listing amazon listing price
     *
     * @param float $price
     * @return $this
     */
    public function setListPrice($price)
    {
        return $this->setData('list_price', $price);
    }

    /**
     * Get listing amazon shipping price
     *
     * @return float|null
     */
    public function getShippingPrice()
    {
        return $this->getData('shipping_price');
    }

    /**
     * Set listing amazon shipping price
     *
     * @param float $price
     * @return $this
     */
    public function setShippingPrice($price)
    {
        return $this->setData('shipping_price', $price);
    }

    /**
     * Get listing amazon landed price
     *
     * @return float|null
     */
    public function getLandedPrice()
    {
        return $this->getData('landed_price');
    }

    /**
     * Set listing amazon landed price
     *
     * @param float $price
     * @return $this
     */
    public function setLandedPrice($price)
    {
        return $this->setData('landed_price', $price);
    }

    /**
     * Get listing amazon MSRP price
     *
     * @return float|null
     */
    public function getMsrpPrice()
    {
        return $this->getData('msrp_price');
    }

    /**
     * Set listing amazon MSRP price
     *
     * @param float $price
     * @return $this
     */
    public function setMsrpPrice($price)
    {
        return $this->setData('msrp_price', $price);
    }

    /**
     * Get listing amazon MAP price
     *
     * @return float|null
     */
    public function getMapPrice()
    {
        return $this->getData('map_price');
    }

    /**
     * Set listing amazon MAP price
     *
     * @param float $price
     * @return $this
     */
    public function setMapPrice($price)
    {
        return $this->setData('map_price', $price);
    }

    /**
     * Get listing variants flag
     *
     * @return int|null
     */
    public function getVariants()
    {
        return $this->getData('variants');
    }

    /**
     * Set listing variants flag
     *
     * @param int $flag
     * @return $this
     */
    public function setVariants($flag)
    {
        return $this->setData('variants', $flag);
    }

    /**
     * Get listing condition
     *
     * @return int|null
     */
    public function getCondition()
    {
        return $this->getData('condition');
    }

    /**
     * Set listing condition
     *
     * @param int $condition
     * @return $this
     */
    public function setCondition($condition)
    {
        return $this->setData('condition', $condition);
    }

    /**
     * Get listing is listed flag
     *
     * @return int|null
     */
    public function getIsListed()
    {
        return $this->getData('is_listed');
    }

    /**
     * Set listing is listed flag
     *
     * @param int $flag
     * @return $this
     */
    public function setIsListed($flag)
    {
        return $this->setData('is_listed', $flag);
    }

    /**
     * Get listing eligibility flag
     *
     * @return int|null
     */
    public function getEligible()
    {
        return $this->getData('eligible');
    }

    /**
     * Set listing eligiblity flag
     *
     * @param int $flag
     * @return $this
     */
    public function setEligible($flag)
    {
        return $this->setData('eligible', $flag);
    }

    /**
     * Get listing fulfilled by
     *
     * @return string|null
     */
    public function getFulfilledBy()
    {
        return $this->getData('fulfilled_by');
    }

    /**
     * Set listing fulfilled by
     *
     * @param string $value
     * @return $this
     */
    public function setFulfilledBy($value)
    {
        return $this->setData('fulfilled_by', $value);
    }

    /**
     * Get listing pricing update flag
     *
     * @return int|null
     */
    public function getPricingUpdate()
    {
        return $this->getData('pricing_update');
    }

    /**
     * Set listing pricing update flag
     *
     * @param int $flag
     * @return $this
     */
    public function setPricingUpdate($flag)
    {
        return $this->setData('pricing_update', $flag);
    }

    /**
     * Get listing quantity update flag
     *
     * @return int|null
     */
    public function getQuantityUpdate()
    {
        return $this->getData('quantity_update');
    }

    /**
     * Set listing quantity update flag
     *
     * @param int $flag
     * @return $this
     */
    public function setQuantityUpdate($flag)
    {
        return $this->setData('quantity_update', $flag);
    }

    /**
     * Get listing fulfilled by update flag
     *
     * @return int|null
     */
    public function getFulfilledByUpdate()
    {
        return $this->getData('fulfilled_by_update');
    }

    /**
     * Set listing fulfilled by update flag
     *
     * @param int $flag
     * @return $this
     */
    public function setFulfilledByUpdate($flag)
    {
        return $this->setData('fulfilled_by_update', $flag);
    }

    /**
     * Get listing shipping calculated flag
     *
     * @return int|null
     */
    public function getIsShip()
    {
        return $this->getData('is_ship');
    }

    /**
     * Set listing shipping calculated flag
     *
     * @param int $flag
     * @return $this
     */
    public function setIsShip($flag)
    {
        return $this->setData('is_ship', $flag);
    }

    /**
     * Get listing status
     *
     * @return int|null
     */
    public function getListStatus()
    {
        return $this->getData('list_status');
    }

    /**
     * Set listing status
     *
     * @param int $status
     * @return $this
     */
    public function setListStatus($status)
    {
        return $this->setData('list_status', $status);
    }

    /**
     * Get listing condition override
     *
     * @return int|null
     */
    public function getConditionOverride()
    {
        return $this->getData('condition_override');
    }

    /**
     * Set listing condition override
     *
     * @param int $flag
     * @return $this
     */
    public function setConditionOverride($flag)
    {
        return $this->setData('condition_override', $flag);
    }

    /**
     * Get listing condition notes override
     *
     * @return string|null
     */
    public function getConditionNotesOverride()
    {
        return $this->getData('condition_notes_override');
    }

    /**
     * Set listing condition notes override
     *
     * @param string $value
     * @return $this
     */
    public function setConditionNotesOverride($value)
    {
        return $this->setData('condition_notes_override', $value);
    }

    /**
     * Get listing amazon listing price override
     *
     * @return float|null
     */
    public function getListPriceOverride()
    {
        return $this->getData('list_price_override');
    }

    /**
     * Set listing amazon listing price override
     *
     * @param float $price
     * @return $this
     */
    public function setListPriceOverride($price)
    {
        return $this->setData('list_price_override', $price);
    }

    /**
     * Get listing handling override
     *
     * @return int|null
     */
    public function getHandlingOverride()
    {
        return $this->getData('handling_override');
    }

    /**
     * Set listing handling override
     *
     * @param int $value
     * @return $this
     */
    public function setHandlingOverride($value)
    {
        return $this->setData('handling_override', $value);
    }
}
