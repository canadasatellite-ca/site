<?php

namespace Brsw\FailurePayment\Model;

class FailurePayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_FAILURE_PAYMENT = 'failurepayment';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_FAILURE_PAYMENT;

    /**
     * @var string
     */
    protected $_infoBlockType = 'Brsw\FailurePayment\Block\Info\FailurePayment';


    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;

}
