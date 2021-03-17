<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Plugin\Quote\Model\Quote;

class Config
{
    /**
     * @var \Mageside\CanadaPostShipping\Helper\Carrier
     */
    protected $carrierHelper;

    /**
     * Config constructor.
     * @param \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper
     */
    public function __construct(\Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper)
    {
        $this->carrierHelper = $carrierHelper;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Config $subject
     * @param array $attributeKeys
     * @return array
     */
    public function afterGetProductAttributes(\Magento\Quote\Model\Quote\Config $subject, array $attributeKeys)
    {
        $attributeCode = $this->carrierHelper->getConfigCarrier('non_mailable_attribute');
        if ($attributeCode !== 'none') {
            $attributeKeys[] = $attributeCode;
        }

        return $attributeKeys;
    }
}
