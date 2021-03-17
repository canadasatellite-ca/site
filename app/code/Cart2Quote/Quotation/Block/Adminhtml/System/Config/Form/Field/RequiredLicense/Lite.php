<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\RequiredLicense;

/**
 * Class Lite
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\RequiredLicense
 */
class Lite extends \Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\RequiredLicense
{
    /**
     * Lite constructor
     * @param \Cart2Quote\Quotation\Helper\Data\LicenseInterface $license
     * @param \Cart2Quote\Quotation\Helper\Data\Metadata $metadata
     * @param \Magento\Backend\Block\Template\Context $context
     * @param string $template
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data\LicenseInterface $license,
        \Cart2Quote\Quotation\Helper\Data\Metadata $metadata,
        \Magento\Backend\Block\Template\Context $context,
        $template = 'Cart2Quote_Quotation::system/config/form/field/requiredLicense/lite.phtml',
        array $data = []
    ) {
        parent::__construct($license, $metadata, $context, $data);
        $this->_template = $template;
    }

    /**
     * Check if the current edtion is should allow this feature
     *
     * @return bool
     */
    public function isAllowedForEdition()
    {
        return $this->license->isAllowedForEdition('lite');
    }
}
