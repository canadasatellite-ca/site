<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Source;

class Generic implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Mageside\CanadaPostShipping\Model\Carrier
     */
    protected $_shippingCanadaPost;

    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = '';

    /**
     * @param \Mageside\CanadaPostShipping\Model\Carrier $shippingCanadaPost
     */
    public function __construct(\Mageside\CanadaPostShipping\Model\Carrier $shippingCanadaPost)
    {
        $this->_shippingCanadaPost = $shippingCanadaPost;
    }

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configData = $this->_shippingCanadaPost->getCode($this->_code);
        $arr = [];
        foreach ($configData as $code => $title) {
            $arr[] = ['value' => $code, 'label' => $title];
        }
        return $arr;
    }
}
