<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api\Data;

/**
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface PricingRuleInterface
{
    /**
     * Get rule id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get rule merchant id
     *
     * @return int|null
     */
    public function getMerchantId();

    /** Set rule merchant id
     *
     * @param int $merchantId
     * @return $this
     */
    public function setMerchantId($merchantId);

    /**
     * Get rule name
     *
     * @return string|null
     */
    public function getName();

    /** Set rule name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get rule description
     *
     * @return string|null
     */
    public function getDescription();

    /** Set rule description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get rule from date
     *
     * @return \DateTime|null
     */
    public function getFromDate();

    /** Set rule from date
     *
     * @param \DateTime $date
     * @return $this
     */
    public function setFromDate($date);

    /**
     * Get rule to date
     *
     * @return \DateTime|null
     */
    public function getToDate();

    /** Set rule to date
     *
     * @param \DateTime $date
     * @return $this
     */
    public function setToDate($date);

    /**
     * Get rule is active status
     *
     * @return int|null
     */
    public function getIsActive();

    /** Set rule is active status
     *
     * @param int $flag
     * @return $this
     */
    public function setIsActive($flag);

    /**
     * Get rule conditions serialized
     *
     * @return string|null
     */
    public function getConditionsSerialized();

    /** Set rule conditions serialized
     *
     * @param string $conditions
     * @return $this
     */
    public function setConditionsSerialized($conditions);

    /**
     * Get stop rule processing flag
     *
     * @return int|null
     */
    public function getStopRulesProcessing();

    /** Set stop rule processing flag
     *
     * @param int $flag
     * @return $this
     */
    public function setStopRulesProcessing($flag);

    /**
     * Get rule sort order
     *
     * @return int|null
     */
    public function getSortOrder();

    /** Set rule sort order
     *
     * @param int $order
     * @return $this
     */
    public function setSortOrder($order);

    /**
     * Get rule price movement
     *
     * @return int|null
     */
    public function getPriceMovement();

    /** Set rule price movement
     *
     * @param int $priceMovement
     * @return $this
     */
    public function setPriceMovement($priceMovement);

    /**
     * Get rule simple action
     *
     * @return int|null
     */
    public function getSimpleAction();

    /** Set rule simple action
     *
     * @param int $simpleAction
     * @return $this
     */
    public function setSimpleAction($simpleAction);

    /**
     * Get rule discount amount
     *
     * @return float|null
     */
    public function getDiscountAmount();

    /** Set rule discount amount
     *
     * @param float $amount
     * @return $this
     */
    public function setDiscountAmount($amount);

    /**
     * Get rule type (intelligent vs standard)
     *
     * @return int|null
     */
    public function getAuto();

    /** Set rule type (intelligent vs standard)
     *
     * @param int $auto
     * @return $this
     */
    public function setAuto($auto);

    /**
     * Get rule intelligent source (best buy box vs lowest price)
     *
     * @return int|null
     */
    public function getAutoSource();

    /** Set rule intelligent source (best buy box vs lowest price)
     *
     * @param int $autoSource
     * @return $this
     */
    public function setAutoSource($autoSource);

    /**
     * Get intelligent rule minimum feedback requirements
     *
     * @return int|null
     */
    public function getAutoMinimumFeedback();

    /** Set intelligent rule minimum feedback requirements
     *
     * @param int $autoMinimumFeedback
     * @return $this
     */
    public function setAutoMinimumFeedback($autoMinimumFeedback);

    /**
     * Get intelligent rule minimum feedback count
     *
     * @return int|null
     */
    public function getAutoFeedbackCount();

    /** Set intelligent rule minimum feedback count
     *
     * @param int $autoFeedbackCount
     * @return $this
     */
    public function setAutoFeedbackCount($autoFeedbackCount);

    /**
     * Get intelligent rule condition setting
     *
     * @return int|null
     */
    public function getAutoCondition();

    /** Set intelligent rule condition setting
     *
     * @param int $autoCondition
     * @return $this
     */
    public function setAutoCondition($autoCondition);

    /**
     * Get intelligent rule new condition variance
     *
     * @return float|null
     */
    public function getNewVariance();

    /** Set intelligent rule new condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setNewVariance($variance);

    /**
     * Get intelligent rule refurbished condition variance
     *
     * @return float|null
     */
    public function getRefurbishedVariance();

    /** Set intelligent rule refurbished condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setRefurbishedVariance($variance);

    /**
     * Get intelligent rule used like new condition variance
     *
     * @return float|null
     */
    public function getUsedlikenewVariance();

    /** Set intelligent rule used like new condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setUsedlikenewVariance($variance);

    /**
     * Get intelligent rule used very good condition variance
     *
     * @return float|null
     */
    public function getUsedverygoodVariance();

    /** Set intelligent rule used very good condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setUsedverygoodVariance($variance);

    /**
     * Get intelligent rule used good condition variance
     *
     * @return float|null
     */
    public function getUsedgoodVariance();

    /** Set intelligent rule used good condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setUsedgoodVariance($variance);

    /**
     * Get intelligent rule used acceptable condition variance
     *
     * @return float|null
     */
    public function getUsedacceptableVariance();

    /** Set intelligent rule used acceptable condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setUsedacceptableVariance($variance);

    /**
     * Get intelligent rule collectible like new condition variance
     *
     * @return float|null
     */
    public function getCollectiblelikenewVariance();

    /** Set intelligent rule collectible like new condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setCollectiblelikenewVariance($variance);

    /**
     * Get intelligent rule collectible very good condition variance
     *
     * @return float|null
     */
    public function getCollectibleverygoodVariance();

    /** Set intelligent rule collectible very good condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setCollectibleverygoodVariance($variance);

    /**
     * Get intelligent rule collectible good condition variance
     *
     * @return float|null
     */
    public function getCollectiblegoodVariance();

    /** Set intelligent rule collectible good condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setCollectiblegoodVariance($variance);

    /**
     * Get intelligent rule collectible acceptable condition variance
     *
     * @return float|null
     */
    public function getCollectibleacceptableVariance();

    /** Set intelligent rule collectible acceptable condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setCollectibleacceptableVariance($variance);

    /**
     * Get intelligent rule floor source
     *
     * @return string|null
     */
    public function getFloor();

    /** Set intelligent rule floor source
     *
     * @param string $source
     * @return $this
     */
    public function setFloor($source);

    /**
     * Get intelligent rule floor price movement (increase, decrease, or match)
     *
     * @return int|null
     */
    public function getFloorPriceMovement();

    /** Set intelligent rule floor price movement (increase, decrease, or match)
     *
     * @param int $value
     * @return $this
     */
    public function setFloorPriceMovement($value);

    /**
     * Get intelligent rule floor simple action
     *
     * @return string|null
     */
    public function getFloorSimpleAction();

    /** Set intelligent rule floor simple action
     *
     * @param string $value
     * @return $this
     */
    public function setFloorSimpleAction($value);

    /**
     * Get intelligent rule floor discount amount
     *
     * @return float|null
     */
    public function getFloorDiscountAmount();

    /** Set intelligent rule floor discount amount
     *
     * @param float $discount
     * @return $this
     */
    public function setFloorDiscountAmount($discount);

    /**
     * Get intelligent rule ceiling source
     *
     * @return string|null
     */
    public function getCeiling();

    /** Set intelligent rule ceiling source
     *
     * @param string $source
     * @return $this
     */
    public function setCeiling($source);

    /**
     * Get intelligent rule ceiling price movement (increase, decrease, or match)
     *
     * @return int|null
     */
    public function getCeilingPriceMovement();

    /** Set intelligent rule ceiling price movement (increase, decrease, or match)
     *
     * @param int $value
     * @return $this
     */
    public function setCeilingPriceMovement($value);

    /**
     * Get intelligent rule ceiling simple action
     *
     * @return string|null
     */
    public function getCeilingSimpleAction();

    /** Set intelligent rule ceiling simple action
     *
     * @param string $value
     * @return $this
     */
    public function setCeilingSimpleAction($value);

    /**
     * Get intelligent rule ceiling discount amount
     *
     * @return float|null
     */
    public function getCeilingDiscountAmount();

    /** Set intelligent rule ceiling discount amount
     *
     * @param float $discount
     * @return $this
     */
    public function setCeilingDiscountAmount($discount);
}
