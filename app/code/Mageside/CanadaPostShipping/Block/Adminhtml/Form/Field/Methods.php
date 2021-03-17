<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Block\Adminhtml\Form\Field;

class Methods extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var \Mageside\CanadaPostShipping\Model\Carrier
     */
    protected $_carrier;

    /**
     * Methods constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Mageside\CanadaPostShipping\Model\Carrier $carrier
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Mageside\CanadaPostShipping\Model\Carrier $carrier,
        array $data = []
    ) {
        $this->_carrier = $carrier;
        parent::__construct($context, $data);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $methods = $this->_carrier->getCode('method');
        foreach ($methods as $code => $label) {
            $this->addOption($code, $label);
        }

        return parent::_toHtml();
    }
}
