<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Button
 */
class Css extends \Magento\Framework\View\Element\Template
{
    /**
     * Xml pah to quoteadv sidebar extra css
     */
    const XML_PATH_QUOTATION_SIDEBAR_EXTRA_CSS = 'cart2quote_advanced/general/extra_global_css';

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Css constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        array $data
    ) {
        $this->quotationHelper = $quotationHelper;
        parent::__construct($context, $data);
    }

    /**
     * Return if the theme is enabled
     */
    public function isThemeEnabled()
    {
        return $this->quotationHelper->isFrontendEnabled();
    }

    /**
     * Return the extra CSS set in the backend
     */
    public function getExtraCSS()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_QUOTATION_SIDEBAR_EXTRA_CSS,
            ScopeInterface::SCOPE_STORE
        );
    }
}
