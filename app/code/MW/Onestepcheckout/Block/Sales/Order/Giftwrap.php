<?php

namespace MW\Onestepcheckout\Block\Sales\Order;

class Giftwrap extends \Magento\Framework\View\Element\Template
{

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Retrive totals source object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Initialize giftwrap totals
     *
     * @return MW\Onestepcheckout\Block\Sales\Order\Giftwrap
     */
    public function initTotals()
    {
        if ((float) $this->getOrder()->getBaseGiftwrapAmount()) {
            $source = $this->getSource();
            $value  = $source->getGiftwrapAmount();

            $this->getParentBlock()->addTotal(new \Magento\Framework\DataObject(
                [
                    'code'   => 'giftwrap_amount',
                    'strong' => false,
                    'label'  => __('Gift Wrap'),
                    'value'  => $value
                ]
            ));
        }

        return $this;
    }
}