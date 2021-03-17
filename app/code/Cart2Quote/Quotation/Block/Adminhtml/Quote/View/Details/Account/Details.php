<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Details\Account;

/**
 * Class Details
 *
 * @package Cart2Quote\Quotation\Block\Quote\Details\Account
 */
class Details extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Info
{
    /**
     * Customer Model
     *
     * @var \Magento\Customer\Model\Customer
     */
    private $customer;

    /**
     * Details constructor
     *
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Quote\Model\ResourceModel\Quote $quoteResourceModel
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadata
     * @param \Magento\Customer\Model\Metadata\ElementFactory $elementFactory
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $_orderCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Quote\Model\ResourceModel\Quote $quoteResourceModel,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\CustomerMetadataInterface $metadata,
        \Magento\Customer\Model\Metadata\ElementFactory $elementFactory,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $_orderCollectionFactory,
        array $data = []
    ) {
        $this->customer = $customer;
        $this->groupRepository = $groupRepository;
        $this->metadata = $metadata;
        $this->_metadataElementFactory = $elementFactory;
        $this->addressRenderer = $addressRenderer;
        $this->_quoteAddressToOrderAddress = $quoteAddressToOrderAddress;
        $this->_orderCollectionFactory = $_orderCollectionFactory;
        parent::__construct(
            $deploymentConfig,
            $quoteResourceModel,
            $quoteCollectionFactory,
            $context,
            $registry,
            $adminHelper,
            $groupRepository,
            $metadata,
            $elementFactory,
            $addressRenderer,
            $quoteAddressToOrderAddress,
            $_orderCollectionFactory,
            $data
        );
    }

    /**
     * Get customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        $customer = $this->getQuote()->getCustomer();

        if ($customer->getId()) {
            $customerName = $this->customer->updateData($customer)->getName();
        } else {
            $customerName = implode(' ', [
                $this->getQuote()->getCustomerFirstname(),
                $this->getQuote()->getCustomerMiddlename(),
                $this->getQuote()->getCustomerLastname(),
            ]);
        }

        return $this->escapeHtml($customerName);
    }

    /**
     * @return string|boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerTelephone()
    {
        $quote = $this->getQuote();
        $billingAddress = $quote->getBillingAddress();
        $shippingAddress = $quote->getShippingAddress();

        if ($billingAddress->getTelephone()) {
            return $billingAddress->getTelephone();
        }

        if ($shippingAddress->getTelephone()) {
            return $shippingAddress->getTelephone();
        }

        return false;
    }
}
