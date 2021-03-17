<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\QuoteCheckout;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Helper\Address as AddressHelper;
use Magento\Customer\Model\Session;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Sales\Api\Data\OrderAddressInterface;

/**
 * Class AttributeMerger
 *
 * @package Cart2Quote\Quotation\Block\Quote\QuoteCheckout
 */
class AttributeMerger extends \Magento\Checkout\Block\Checkout\AttributeMerger
{
    /**
     * @var AddressHelper
     */
    private $addressHelper;

    /**
     * AttributeMerger constructor
     *
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Directory\Helper\Data $directoryHelper
     */
    public function __construct(
        AddressHelper $addressHelper,
        Session $customerSession,
        CustomerRepository $customerRepository,
        DirectoryHelper $directoryHelper
    ) {
        $this->addressHelper = $addressHelper;

        parent::__construct(
            $addressHelper,
            $customerSession,
            $customerRepository,
            $directoryHelper
        );
    }

    /**
     * Check if address attribute is visible on frontend
     * - Overwrite from parent to avoid the VAT is required error on quote request
     * - when frontend visibility is set to no.
     *
     * @param string $attributeCode
     * @param array $attributeConfig
     * @param array $additionalConfig field configuration provided via layout XML
     * @return bool
     */
    protected function isFieldVisible($attributeCode, array $attributeConfig, array $additionalConfig = [])
    {
        if ($attributeCode != OrderAddressInterface::VAT_ID) {
            return parent::isFieldVisible($attributeCode, $attributeConfig, $additionalConfig);
        } else {
            $taxVatVisible = $this->addressHelper->isVatAttributeVisible();
            $showTaxVat = (bool)$this->addressHelper->getConfig('taxvat_show');

            if (!$taxVatVisible && !$showTaxVat) {
                return false;
            }
        }

        return true;
    }
}
