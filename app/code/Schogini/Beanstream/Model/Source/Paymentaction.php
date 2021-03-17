<?php
namespace Schogini\Beanstream\Model\Source;
class Paymentaction
{
    public function toOptionArray()
    {
        return array(array('value' => \Magento\Payment\Model\Method\Cc::ACTION_AUTHORIZE, 'label' => 'Authorize Only'), array('value' => \Magento\Payment\Model\Method\Cc::ACTION_AUTHORIZE_CAPTURE, 'label' => 'Sale'));
    }
}