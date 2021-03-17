<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Listing;

use Magento\Amazon\Api\Data\ListingRuleInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Rule
 */
class Rule extends AbstractModel implements ListingRuleInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Listing\Rule::class
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
     * Get merchant id
     *
     * @return int|null
     */
    public function getMerchantId()
    {
        return $this->getData('merchant_id');
    }

    /**
     * Set merchant id
     *
     * @param int $id
     * @return $this
     */
    public function setMerchantId($id)
    {
        return $this->setData('merchant_id', $id);
    }

    /**
     * Get website id
     *
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->getData('website_id');
    }

    /**
     * Set website id
     *
     * @param int $id
     * @return void
     */
    public function setWebsiteId(int $id)
    {
        $this->setData('website_id', $id);
    }

    /**
     * Get assigned rule conditions
     *
     * @return string
     */
    public function getConditionsSerialized()
    {
        return $this->getData('conditions_serialized');
    }

    /**
     * Set assigned rule conditions
     *
     * @param string $conditions
     * @return $this
     */
    public function setConditionsSerialized($conditions)
    {
        return $this->setData('conditions_serialized', $conditions);
    }
}
