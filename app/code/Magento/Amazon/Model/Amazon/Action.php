<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon;

use Magento\Amazon\Api\Data\ActionInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Action
 */
class Action extends AbstractModel implements ActionInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Action::class
        );
    }

    /**
     * Get action id
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
     * @return string|null
     */
    public function getMerchantId()
    {
        return $this->getData('merchant_id');
    }

    /**
     * Set merchant id
     *
     * @param string $id
     * @return $this
     */
    public function setMerchantId($id)
    {
        return $this->setData('merchant_id', $id);
    }

    /**
     * Get unique identifier
     *
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->getData('identifier');
    }

    /**
     * Set unique identifier
     *
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        return $this->setData('identifier', $identifier);
    }
}
