<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Block;

class Offices extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mageside\CanadaPostShipping\Helper\Carrier
     */
    protected $_carrierHelper;

    /**
     * Offices constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper,
        array $data = []
    ) {
        $this->_carrierHelper = $carrierHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return bool|mixed
     */
    public function canShowD2po()
    {
        return $this->_carrierHelper->getConfigCarrier('enable_d2po');
    }

    /**
     * @return bool|mixed
     */
    public function getGoogleMapsApiKey()
    {
        if (!$this->canShowD2po()) {
            return false;
        }

        if (!$this->_carrierHelper->getConfigCarrier('google_maps_api_key')) {
            return false;
        }

        return $this->_carrierHelper->getConfigCarrier('google_maps_api_key');
    }
}
