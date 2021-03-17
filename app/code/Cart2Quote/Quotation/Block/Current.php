<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block;

/**
 * Class Current
 * @package Cart2Quote\Quotation\Block
 */
class Current extends \Magento\Framework\View\Element\Html\Link\Current implements \Magento\Customer\Block\Account\SortLinkInterface
{
    /**
     * @var bool
     */
    protected $_visibilityEnabled;

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Current constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        array $data
    ) {
        $this->quotationHelper = $quotationHelper;
        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getIsQuotationEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * Check if Cart2Quote visibility is enabled
     *
     * @return bool
     */
    public function getIsQuotationEnabled()
    {
        if (isset($this->_visibilityEnabled)) {
            return $this->_visibilityEnabled;
        }

        if ($this->quotationHelper->isFrontendEnabled()) {
            $this->_visibilityEnabled = true;
            return true;
        }

        $this->_visibilityEnabled = false;
        return false;
    }

    /**
     * Function that makes this block sortable
     *
     * @return int|mixed
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }
}
