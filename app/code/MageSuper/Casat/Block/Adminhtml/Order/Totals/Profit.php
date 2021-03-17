<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Block\Adminhtml\Order\Totals;

/**
 * Totals item block
 */
class Profit extends \Magento\Sales\Block\Adminhtml\Order\Totals
{
    /**
     * Determine display parameters before rendering HTML
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        $this->setCanDisplayProfit(true);

        return $this;
    }

    /**
     * Initialize totals object
     *
     * @return $this
     */
    public function initTotals()
    {
        $total = new \Magento\Framework\DataObject(
            [
                'code' => $this->getNameInLayout(),
                'block_name' => $this->getNameInLayout(),
                'area' => $this->getDisplayArea(),
                'strong' => $this->getStrong(),
            ]
        );
        $this->getParentBlock()->addTotal($total, $this->getAfterCondition());
        return $this;
    }

    /**
     * Price HTML getter
     *
     * @param float $baseAmount
     * @param float $amount
     * @return string
     */
    public function displayPrices($baseAmount, $amount)
    {
        return $this->_adminHelper->displayPrices($this->getOrder(), $baseAmount, $amount);
    }

    /**
     * Price attribute HTML getter
     *
     * @param string $code
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>')
    {
        return $this->_adminHelper->displayPriceAttribute($this->getSource(), $code, $strong, $separator);
    }

    /**
     * Source order getter
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }
}
