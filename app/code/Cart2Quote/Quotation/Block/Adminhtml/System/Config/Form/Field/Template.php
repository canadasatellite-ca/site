<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class Template
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field
 */
class Template extends \Magento\Config\Block\System\Config\Form\Field implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var array
     */
    protected $elementConfigFields = [];

    /**
     * @var \Cart2Quote\Quotation\Helper\Data\Metadata
     */
    protected $metadata;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data\LicenseInterface
     */
    protected $license;

    /**
     * Template constructor.
     * @param \Cart2Quote\Quotation\Helper\Data\LicenseInterface $license
     * @param \Cart2Quote\Quotation\Helper\Data\Metadata $metadata
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data\LicenseInterface $license,
        \Cart2Quote\Quotation\Helper\Data\Metadata $metadata,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->metadata = $metadata;
        $this->license = $license;
    }

    /**
     * @return string
     */
    public function getCart2quoteComparisonUrl()
    {
        if ($this->license->isUnreachable() || $this->license->isMpVersion()) {
            return 'https://marketplace.magento.com/partner/Cart2Quote#partner.products.info';
        }

        return sprintf('%s/%s',
            $this->getCart2QuoteUrl(),
            'magento2-quotation-module-editions.html#compare-cart2quote-plans-m2'
        );
    }

    /**
     * Get C2Q base url
     *
     * @return string
     */
    public function getCart2QuoteUrl()
    {
        if ($this->license->isUnreachable() || $this->license->isMpVersion()) {
            return 'https://marketplace.magento.com/partner/Cart2Quote';
        }

        return 'https://www.cart2quote.com';
    }

    /**
     * Get C2Q support page url
     *
     * @return string
     */
    public function getCart2QuoteSupportPageUrl()
    {
        return sprintf('%s/%s', $this->getCart2QuoteUrl(), 'magento-quotation-module-support.html');
    }

    /**
     * Get C2Q update upgrade page url
     *
     * @return string
     */
    public function getCart2QuoteUpgradeUrl()
    {
        return sprintf('%s/%s', $this->getCart2QuoteUrl(), 'cart2quote-update-upgrade.html');
    }

    /**
     * @param $version
     * @return string
     */
    public function getCart2QuotReleaseNotesUrl($version)
    {
        $version = \str_replace('.', '', $version);

        return sprintf(
            '%s/%s',
            $this->getCart2QuoteUrl(),
            \sprintf('release-notes-magento2#r%s', $version)
        );
    }

    /**
     * Get zendesk update page url
     *
     * @return string
     */
    public function getCart2QuoteUpdateUrl()
    {
        return 'https://cart2quote.zendesk.com/hc/en-us/articles/360029662732';
    }

    /**
     * Render function
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        $this->applyElementConfig($element);

        return parent::render($element);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     */
    protected function applyElementConfig(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $config = $element->getFieldConfig();
        foreach ($this->elementConfigFields as $configField) {
            if ($config[$configField]) {
                if (is_array($config[$configField])) {
                    if (isset($config[$configField]['value'])) {
                        if (isset($config[$configField]['translate']) && $config[$configField]['translate'] == true) {
                            $element->setData($configField, __($config[$configField]['value']));
                        } else {
                            $element->setData($configField, $config[$configField]['value']);
                        }
                    }
                } else {
                    $element->setData($configField, $config[$configField]);
                }
            }
        }
    }

    /**
     * Get link ellement
     *
     * @param string $href
     * @param string $value
     * @param string $target
     * @return string
     */
    public function getLink($href, $value, $target = '_blank')
    {
        return sprintf('<a href="%s" target="%s">%s</a>', $href, $target, $value);
    }

    /**
     * Render element value
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($element->getTooltip()) {
            $html = '<td class="value with-tooltip">';
            $html .= $this->getElementHtml($element);
            $html .= '<div class="tooltip"><span class="help"><span></span></span>';
            $html .= '<div class="tooltip-content">' . $element->getTooltip() . '</div></div>';
        } else {
            $html = '<td class="value">';
            $html .= $this->getElementHtml($element);
        }
        if ($element->getComment()) {
            $html .= '<p class="note value-container"><span>' . $element->getComment() . '</span></p>';
        }
        $html .= '</td>';

        return $html;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    private function getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return \sprintf(
            '%s%s%s',
            $this->beforeGetElementHtml(),
            $this->_getElementHtml($element),
            $this->afterGetElementHtml()
        );
    }

    /**
     * @return string
     */
    protected function beforeGetElementHtml()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function afterGetElementHtml()
    {
        return '';
    }

    /**
     * @return bool|string
     */
    public function getCurrentVersion()
    {
        return $this->metadata->getCart2QuoteVersion();
    }
}