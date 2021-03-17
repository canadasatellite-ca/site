<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Helper;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Directory\Model\Country\Format;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Customer address helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_PHONE = 'general/store_information/phone';
    /**
     * Retrieve disable auto group assign default value
     *
     * @return bool
     */
    public function getPhone()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}
