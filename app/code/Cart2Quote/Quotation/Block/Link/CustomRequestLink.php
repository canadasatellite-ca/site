<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Link;

use CustomRequestForm;

/**
 * Class CustomRequestLink
 *
 * @package Cart2Quote\Quotation\Block\Link
 */
class CustomRequestLink extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * Custom Form Request sku
     */
    const CUSTOM_REQUEST_FORM = 'custom-request-form';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    private $quotationHelper;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * CustomRequestLink constructor.
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        array $data = []

    ) {
        parent::__construct($context, $data);
        $this->quotationHelper = $quotationHelper;
        $this->logger = $context->getLogger();
        $this->productRepository = $productRepository;
        $this->scopeConfig = $context->getScopeConfig();
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @return string|false
     */
    public function getCustomRequestUrl()
    {
        try {
            $customRequest = $this->productRepository->get(self::CUSTOM_REQUEST_FORM);
            if (($this->stockRegistry->getStockItemBySku(self::CUSTOM_REQUEST_FORM)->getIsInStock()) &&
                ($customRequest->getStatus() == '1')) {
                $url = sprintf("%s%s.html", $this->getUrl(), $customRequest->getUrlKey());

                return $url;
            }
        } catch (\Exception $e) {
            $this->logger->debug(
                __(
                    'Error finding url. No ‘%1’ product in catalog. See %2 for solution.',
                    $this->getCustomRequestLabel(),
                    $this->quotationHelper->getBlogUrl())
                );

            return false;
        }
    }

    /**
     * @return bool
     */
    public function formIsEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            \Cart2Quote\Quotation\Helper\Data::XML_PATH_ENABLE_CUSTOM_REQUEST_FORM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getCustomRequestLabel()
    {
        if ($this->quotationHelper->getCustomRequestName()) {
            return $this->quotationHelper->getCustomRequestName();
        } else {
            return __("Custom Request Form");
        }
    }

    /**
     * Render block HTML.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        return sprintf(
            "<li><a %s > %s </a></li>",
            $this->getLinkAttributes(),
            $this->escapeHtml($this->getLabel()));
    }
}
