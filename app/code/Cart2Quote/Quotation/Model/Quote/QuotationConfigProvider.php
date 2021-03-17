<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote;

use Magento\Framework\App\ObjectManager;

/**
 * Class QuotationConfigProvider
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
class QuotationConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\QuotationConfigProvider {
        getConfig as private traitGetConfig;
        prepareAddressConfig as private traitPrepareAddressConfig;
        prepareDataField as private traitPrepareDataField;
    }

    /**
     * Quote Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $session;

    /**
     * Helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $helper;

    /**
     * QuotationConfigProvider constructor.
     *
     * @param \Cart2Quote\Quotation\Model\Session $session
     * @param \Cart2Quote\Quotation\Helper\Address $helper
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Session $session,
        \Cart2Quote\Quotation\Helper\Address $helper
    ) {
        $this->helper = $helper;
        $this->session = $session;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->traitGetConfig();
    }

    /**
     * Add config fields regarding the shipping and billing configuration
     *
     * @return void
     */
    private function prepareAddressConfig()
    {
        $this->traitPrepareAddressConfig();
    }

    /**
     * Prepare the session data field
     *
     * @param string $fieldName
     * @return array
     */
    private function prepareDataField($fieldName)
    {
        return $this->traitPrepareDataField($fieldName);
    }
}
