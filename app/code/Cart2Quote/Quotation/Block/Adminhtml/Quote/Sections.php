<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote;

/**
 * Class Sections
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote
 */
class Sections extends AbstractQuote
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * Sections constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * Get JSON config
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $sections = $this->getQuote()->getExtensionAttributes()->getSections();
        $config = [];
        foreach ($sections as $section) {
            if ($section->getIsUnassigned()) {
                continue;
            }
            $config['sections'][] = $section->getData();
        }
        $config['quote_id'] = $this->getQuote()->getId();
        $config['action_url'] = $this->getUrl('quotation/sections/save');

        return $this->jsonEncoder->encode($config);
    }
}
