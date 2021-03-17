<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Pricing;

use Magento\Amazon\Api\Data\PricingRuleInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Rule
 */
class Rule extends AbstractModel implements PricingRuleInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Rule::class
        );
    }

    /**
     * Get rule id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Get rule merchant id
     *
     * @return int|null
     */
    public function getMerchantId()
    {
        return $this->getData('merchant_id');
    }

    /** Set rule merchant id
     *
     * @param int $merchantId
     * @return $this
     */
    public function setMerchantId($merchantId)
    {
        return $this->setData('merchant_id', $merchantId);
    }

    /**
     * Get rule name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /** Set rule name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData('name', $name);
    }

    /**
     * Get rule description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData('description');
    }

    /** Set rule description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->setData('description', $description);
    }

    /**
     * Get rule from date
     *
     * @return \DateTime|null
     */
    public function getFromDate()
    {
        return $this->getData('from_date');
    }

    /** Set rule from date
     *
     * @param \DateTime $date
     * @return $this
     */
    public function setFromDate($date)
    {
        return $this->setData('from_date', $date);
    }

    /**
     * Get rule to date
     *
     * @return \DateTime|null
     */
    public function getToDate()
    {
        return $this->getData('to_date');
    }

    /** Set rule to date
     *
     * @param \DateTime $date
     * @return $this
     */
    public function setToDate($date)
    {
        return $this->setData('to_date', $date);
    }

    /**
     * Get rule is active status
     *
     * @return int|null
     */
    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    /** Set rule is active status
     *
     * @param int $flag
     * @return $this
     */
    public function setIsActive($flag)
    {
        return $this->setData('is_active', $flag);
    }

    /**
     * Get rule conditions serialized
     *
     * @return string|null
     */
    public function getConditionsSerialized()
    {
        return $this->getData('conditions_serialized');
    }

    /** Set rule conditions serialized
     *
     * @param string $conditions
     * @return $this
     */
    public function setConditionsSerialized($conditions)
    {
        return $this->setData('conditions_serialized', $conditions);
    }

    /**
     * Get stop rule processing flag
     *
     * @return int|null
     */
    public function getStopRulesProcessing()
    {
        return $this->getData('stop_rules_processing');
    }

    /** Set stop rule processing flag
     *
     * @param int $flag
     * @return $this
     */
    public function setStopRulesProcessing($flag)
    {
        return $this->setData('stop_rules_processing', $flag);
    }

    /**
     * Get rule sort order
     *
     * @return int|null
     */
    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    /** Set rule sort order
     *
     * @param int $order
     * @return $this
     */
    public function setSortOrder($order)
    {
        return $this->setData('sort_order', $order);
    }

    /**
     * Get rule price movement
     *
     * @return int|null
     */
    public function getPriceMovement()
    {
        return $this->getData('price_movement');
    }

    /** Set rule price movement
     *
     * @param int $priceMovement
     * @return $this
     */
    public function setPriceMovement($priceMovement)
    {
        return $this->setData('price_movement', $priceMovement);
    }

    /**
     * Get rule simple action
     *
     * @return int|null
     */
    public function getSimpleAction()
    {
        return $this->getData('simple_action');
    }

    /** Set rule simple action
     *
     * @param int $simpleAction
     * @return $this
     */
    public function setSimpleAction($simpleAction)
    {
        return $this->setData('simple_action', $simpleAction);
    }

    /**
     * Get rule discount amount
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->getData('discount_amount');
    }

    /** Set rule discount amount
     *
     * @param float $amount
     * @return $this
     */
    public function setDiscountAmount($amount)
    {
        return $this->setData('discount_amount', $amount);
    }

    /**
     * Get rule type (intelligent vs standard)
     *
     * @return int|null
     */
    public function getAuto()
    {
        return $this->getData('auto');
    }

    /** Set rule type (intelligent vs standard)
     *
     * @param int $auto
     * @return $this
     */
    public function setAuto($auto)
    {
        return $this->setData('auto', $auto);
    }

    /**
     * Get rule intelligent source (best buy box vs lowest price)
     *
     * @return int|null
     */
    public function getAutoSource()
    {
        return $this->getData('auto_source');
    }

    /** Set rule intelligent source (best buy box vs lowest price)
     *
     * @param int $autoSource
     * @return $this
     */
    public function setAutoSource($autoSource)
    {
        return $this->setData('auto_source', $autoSource);
    }

    /**
     * Get intelligent rule minimum feedback requirements
     *
     * @return int|null
     */
    public function getAutoMinimumFeedback()
    {
        return $this->getData('auto_minimum_feedback');
    }

    /** Set intelligent rule minimum feedback requirements
     *
     * @param int $autoMinimumFeedback
     * @return $this
     */
    public function setAutoMinimumFeedback($autoMinimumFeedback)
    {
        return $this->setData('auto_minimum_feedback', $autoMinimumFeedback);
    }

    /**
     * Get intelligent rule minimum feedback count
     *
     * @return int|null
     */
    public function getAutoFeedbackCount()
    {
        return $this->getData('auto_feedback_count');
    }

    /** Set intelligent rule minimum feedback count
     *
     * @param int $autoFeedbackCount
     * @return $this
     */
    public function setAutoFeedbackCount($autoFeedbackCount)
    {
        return $this->setData('auto_feedback_count', $autoFeedbackCount);
    }

    /**
     * Get intelligent rule condition setting
     *
     * @return int|null
     */
    public function getAutoCondition()
    {
        return $this->getData('auto_condition');
    }

    /** Set intelligent rule condition setting
     *
     * @param int $autoCondition
     * @return $this
     */
    public function setAutoCondition($autoCondition)
    {
        return $this->setData('auto_condition', $autoCondition);
    }

    /**
     * Get intelligent rule new condition variance
     *
     * @return float|null
     */
    public function getNewVariance()
    {
        return $this->getData('new_variance');
    }

    /** Set intelligent rule new condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setNewVariance($variance)
    {
        return $this->setData('new_variance', $variance);
    }

    /**
     * Get intelligent rule refurbished condition variance
     *
     * @return float|null
     */
    public function getRefurbishedVariance()
    {
        return $this->getData('refurbished_variance');
    }

    /** Set intelligent rule refurbished condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setRefurbishedVariance($variance)
    {
        return $this->setData('refurbished_variance', $variance);
    }

    /**
     * Get intelligent rule used like new condition variance
     *
     * @return float|null
     */
    public function getUsedlikenewVariance()
    {
        return $this->getData('usedlikenew_variance');
    }

    /** Set intelligent rule used like new condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setUsedlikenewVariance($variance)
    {
        return $this->setData('usedlikenew_variance', $variance);
    }

    /**
     * Get intelligent rule used very good condition variance
     *
     * @return float|null
     */
    public function getUsedverygoodVariance()
    {
        return $this->getData('usedverygood_variance');
    }

    /** Set intelligent rule used very good condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setUsedverygoodVariance($variance)
    {
        return $this->setData('usedverygood_variance', $variance);
    }

    /**
     * Get intelligent rule used good condition variance
     *
     * @return float|null
     */
    public function getUsedgoodVariance()
    {
        return $this->getData('usedgood_variance');
    }

    /** Set intelligent rule used good condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setUsedgoodVariance($variance)
    {
        return $this->setData('usedgood_variance', $variance);
    }

    /**
     * Get intelligent rule used acceptable condition variance
     *
     * @return float|null
     */
    public function getUsedacceptableVariance()
    {
        return $this->getData('usedacceptable_variance');
    }

    /** Set intelligent rule used acceptable condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setUsedacceptableVariance($variance)
    {
        return $this->setData('usedacceptable_variance', $variance);
    }

    /**
     * Get intelligent rule collectible like new condition variance
     *
     * @return float|null
     */
    public function getCollectiblelikenewVariance()
    {
        return $this->getData('collectiblelikenew_variance');
    }

    /** Set intelligent rule collectible like new condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setCollectiblelikenewVariance($variance)
    {
        return $this->setData('collectiblelikenew_variance', $variance);
    }

    /**
     * Get intelligent rule collectible very good condition variance
     *
     * @return float|null
     */
    public function getCollectibleverygoodVariance()
    {
        return $this->getData('collectibleverygood_variance');
    }

    /** Set intelligent rule collectible very good condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setCollectibleverygoodVariance($variance)
    {
        return $this->setData('collectibleverygood_variance', $variance);
    }

    /**
     * Get intelligent rule collectible good condition variance
     *
     * @return float|null
     */
    public function getCollectiblegoodVariance()
    {
        return $this->getData('collectiblegood_variance');
    }

    /** Set intelligent rule collectible good condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setCollectiblegoodVariance($variance)
    {
        return $this->setData('collectiblegood_variance', $variance);
    }

    /**
     * Get intelligent rule collectible acceptable condition variance
     *
     * @return float|null
     */
    public function getCollectibleacceptableVariance()
    {
        return $this->getData('collectibleacceptable_variance');
    }

    /** Set intelligent rule collectible acceptable condition variance
     *
     * @param float $variance
     * @return $this
     */
    public function setCollectibleacceptableVariance($variance)
    {
        return $this->setData('collectibleacceptable_variance', $variance);
    }

    /**
     * Get intelligent rule floor source
     *
     * @return string|null
     */
    public function getFloor()
    {
        return $this->getData('floor');
    }

    /** Set intelligent rule floor source
     *
     * @param string $source
     * @return $this
     */
    public function setFloor($source)
    {
        return $this->setData('floor', $source);
    }

    /**
     * Get intelligent rule floor price movement (increase, decrease, or match)
     *
     * @return int|null
     */
    public function getFloorPriceMovement()
    {
        return $this->getData('floor_price_movement');
    }

    /** Set intelligent rule floor price movement (increase, decrease, or match)
     *
     * @param int $value
     * @return $this
     */
    public function setFloorPriceMovement($value)
    {
        return $this->setData('floor_price_movement', $value);
    }

    /**
     * Get intelligent rule floor simple action
     *
     * @return string|null
     */
    public function getFloorSimpleAction()
    {
        return $this->getData('floor_simple_action');
    }

    /** Set intelligent rule floor simple action
     *
     * @param string $value
     * @return $this
     */
    public function setFloorSimpleAction($value)
    {
        return $this->setData('floor_simple_action', $value);
    }

    /**
     * Get intelligent rule floor discount amount
     *
     * @return float|null
     */
    public function getFloorDiscountAmount()
    {
        return $this->getData('floor_discount_amount');
    }

    /** Set intelligent rule floor discount amount
     *
     * @param float $discount
     * @return $this
     */
    public function setFloorDiscountAmount($discount)
    {
        return $this->setData('floor_discount_amount', $discount);
    }

    /**
     * Get intelligent rule ceiling source
     *
     * @return string|null
     */
    public function getCeiling()
    {
        return $this->getData('ceiling');
    }

    /** Set intelligent rule ceiling source
     *
     * @param string $source
     * @return $this
     */
    public function setCeiling($source)
    {
        return $this->setData('ceiling', $source);
    }

    /**
     * Get intelligent rule ceiling price movement (increase, decrease, or match)
     *
     * @return int|null
     */
    public function getCeilingPriceMovement()
    {
        return $this->getData('ceiling_price_movement');
    }

    /** Set intelligent rule ceiling price movement (increase, decrease, or match)
     *
     * @param int $value
     * @return $this
     */
    public function setCeilingPriceMovement($value)
    {
        return $this->setData('ceiling_price_movement', $value);
    }

    /**
     * Get intelligent rule ceiling simple action
     *
     * @return string|null
     */
    public function getCeilingSimpleAction()
    {
        return $this->getData('ceiling_simple_action');
    }

    /** Set intelligent rule ceiling simple action
     *
     * @param string $value
     * @return $this
     */
    public function setCeilingSimpleAction($value)
    {
        return $this->setData('ceiling_simple_action', $value);
    }

    /**
     * Get intelligent rule ceiling discount amount
     *
     * @return float|null
     */
    public function getCeilingDiscountAmount()
    {
        return $this->getData('ceiling_discount_amount');
    }

    /** Set intelligent rule ceiling discount amount
     *
     * @param float $discount
     * @return $this
     */
    public function setCeilingDiscountAmount($discount)
    {
        return $this->setData('ceiling_discount_amount', $discount);
    }
}
