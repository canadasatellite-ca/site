<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ProductMetadataInterface;

class Analytics extends Template
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * Analytics constructor.
     * @param ProductMetadataInterface $productMetadata
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return bool
     */
    public function injectAdobeOmega(): bool
    {
        return version_compare($this->productMetadata->getVersion(), '2.3.3', '<');
    }
}
