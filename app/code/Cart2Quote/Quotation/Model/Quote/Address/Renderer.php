<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Address;

/**
 * Class Renderer used for formatting the quote address
 */
class Renderer extends \Magento\Sales\Model\Order\Address\Renderer
{
    use \Cart2Quote\Features\Traits\Model\Quote\Address\Renderer {
        formatQuoteAddress as private traitFormatQuoteAddress;
    }

    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $addressConfig;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Quote\Model\Quote\Address\ToOrderAddress
     */
    protected $_quoteAddressToOrderAddress;

    /**
     * Renderer constructor.
     *
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress
     */
    public function __construct(
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress
    ) {
        $this->addressConfig = $addressConfig;
        $this->eventManager = $eventManager;
        $this->_quoteAddressToOrderAddress = $quoteAddressToOrderAddress;
    }

    /**
     * Format quote address like magento formats the order addresses
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param string $type
     * @return null|string
     */
    public function formatQuoteAddress(\Magento\Quote\Model\Quote\Address $address, $type)
    {
        return $this->traitFormatQuoteAddress($address, $type);
    }
}
