<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\Support;

use Magento\Config\Block\System\Config\Form\Field as FormField;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
 * Class DocumentationInfo
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\Support
 */
class DocumentationInfo extends FormField implements RendererInterface
{
    const C2QURL_DOC = 'https://cart2quote.zendesk.com/hc/en-us/articles/';
    const CART2QUOTE_M2_INSTALL_MANUAL = self::C2QURL_DOC . '360028907231-Installation-Manual-Cart2Quote';
    const CART2QUOTE_M2_MP_INSTALL_MANUAL = self::C2QURL_DOC . '360031397851-Installation-via-Magento-Marketplace';
    const CART2QUOTE_M2_UPDATE_MANUAL = self::C2QURL_DOC . '360029662732-How-to-update-Cart2Quote';
    const CART2QUOTE_M2_USER_MANUAL = self::C2QURL_DOC . '360029305592-User-Manual-Cart2Quote';
    const CART2QUOTE_M2_API_MANUAL = 'https://www.cart2quote.com/media/cart2quote-magento2-api';
    const SALESREP_M2_INSTALL_MANUAL = self::C2QURL_DOC . '360029627331-Installation-Manual-SalesRep-Module';

    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '<p>';

        $html .= '<li><a href="' . self::CART2QUOTE_M2_INSTALL_MANUAL . '">';
        $html .= __('Cart2Quote - Installation Manual (FTP/Composer)');
        $html .= '</a></li>';

        $html .= '<li><a href="' . self::CART2QUOTE_M2_MP_INSTALL_MANUAL . '">';
        $html .= __('Cart2Quote - Installation Manual (via Magento Marketplace)');
        $html .= '</a></li>';

        $html .= '<li><a href="' . self::CART2QUOTE_M2_USER_MANUAL . '">';
        $html .= __('Cart2Quote - User Manual');
        $html .= '</a></li>';

        $html .= '<li><a href="' . self::CART2QUOTE_M2_UPDATE_MANUAL . '">';
        $html .= __('Cart2Quote - Update Manual');
        $html .= '</a></li>';

        $html .= '<li><a href="' . self::CART2QUOTE_M2_API_MANUAL . '">';
        $html .= __('Cart2Quote - API Manual');
        $html .= '</a></li>';

        $html .= '<li><a href="' . self::SALESREP_M2_INSTALL_MANUAL . '">';
        $html .= __('SalesRep - Installation Manual');
        $html .= '</a></li>';

        $html .= '</p>';
        $html .= $this->_renderValue($element);

        return $this->_decorateRowHtml($element, $html);
    }
}
