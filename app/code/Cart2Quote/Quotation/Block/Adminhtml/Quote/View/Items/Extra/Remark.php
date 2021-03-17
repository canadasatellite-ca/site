<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Extra;

/**
 * Class Remark
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Extra
 */
class Remark extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Grid
{

    /**
     * Get Item Id
     *
     * @return int
     */
    public function getItemId()
    {
        return $this->getItem()->getId();
    }

    /**
     * Get Quote Item
     *
     * @return \Magento\Quote\Model\Quote\Item
     * @throws \Exception
     */
    public function getItem()
    {
        if ($parentBlock = $this->getParentBlock() && $this->getParentBlock()->getItem()) {
            return $this->getParentBlock()->getItem();
        } else {
            throw new \Exception(__('Unable to load quote item on remark block'));
        }
    }

    /**
     * Get the current tier item on the quote item
     *
     * @return \Cart2Quote\Quotation\Model\Quote\TierItem
     */
    public function getTierItem()
    {
        return $this->getItem()->getCurrentTierItem();
    }

    /**
     * Get the current tier item id
     *
     * @return int
     */
    public function getTierItemId()
    {
        $tierItem = $this->getTierItem();
        if ($tierItem) {
            return $tierItem->getId();
        }

        return 0;
    }

    /**
     * Get the HTML to hide the description
     *
     * @return string
     */
    public function getDescriptionDisableInput()
    {
        $html = '';

        if (!$this->getItemDescription()) {
            $html = 'disabled="disabled"';
        }

        return $html;
    }

    /**
     * Get Item Description
     *
     * @return string
     */
    public function getItemDescription()
    {
        return $this->getItem()->getDescription();
    }

    /**
     * Get hide input css
     *
     * @return string
     */
    public function getDescriptionHideInput()
    {
        $html = '';

        if (!$this->getItemDescription()) {
            $html = 'display: none';
        }

        return $html;
    }

    /**
     * Get checked html if there is an item description
     *
     * @return string
     */
    public function getDescriptionChecked()
    {
        $html = '';

        if ($this->getItemDescription()) {
            $html = 'checked';
        }

        return $html;
    }
}
