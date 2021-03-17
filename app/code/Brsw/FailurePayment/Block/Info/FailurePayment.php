<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Brsw\FailurePayment\Block\Info;

class FailurePayment extends \Magento\Payment\Block\Info
{
    /**
     * @var string
     */
    protected $_template = 'Brsw_FailurePayment::info/failurepayment.phtml';

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Brsw_FailurePayment::info/pdf/failurepayment.phtml');
        return $this->toHtml();
    }
}
