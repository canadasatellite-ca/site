<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote;

/**
 * Quotation quote link
 */
class Link extends \Magento\Framework\View\Element\Html\Link\Current
{
    /** @var \Magento\Framework\Registry */
    protected $_registry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->_registry = $registry;
    }

    /**
     * Get href
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl($this->getPath(), ['quote_id' => $this->getQuote()->getId()]);
    }

    /**
     * Retrieve current quote model instance
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    protected function getQuote()
    {
        return $this->_registry->registry('current_quote');
    }

    /**
     * To HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->hasKey()
            && method_exists($this->getQuote(), 'has' . $this->getKey())
            && !$this->getOrder()->{'has' . $this->getKey()}()
        ) {
            return '';
        }
        return parent::_toHtml();
    }
}
