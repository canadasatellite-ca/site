<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Catalog\Product\View\Type;

use Magento\Framework\App\ObjectManager;

/**
 * @api
 */
class Grouped extends \Magento\GroupedProduct\Block\Product\View\Type\Grouped
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    private $quotationHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Grouped constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Customer\Model\Session $customerSession = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );

        $this->quotationHelper = $quotationHelper;
        $this->customerSession = $customerSession ?: ObjectManager::getInstance()->get(\Magento\Customer\Model\Session::class);
    }

    /**
     * Check if button can be shown
     *
     * @return bool
     */
    public function showButton()
    {
        return $this->quotationHelper->showButtonOnProductView(
            $this->getProduct(),
            $this->customerSession->getCustomerGroupId()
        );
    }
}
