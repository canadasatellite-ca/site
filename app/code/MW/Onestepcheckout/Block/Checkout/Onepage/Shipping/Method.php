<?php

namespace MW\Onestepcheckout\Block\Checkout\Onepage\Shipping;

class Method extends \MW\Onestepcheckout\Block\Checkout\Onepage\AbstractOnepage
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData(
            'shipping_method',
            ['label' => __('Shipping Method'), 'is_show' => $this->isShow()]
        );
        parent::_construct();
    }

    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }
}
