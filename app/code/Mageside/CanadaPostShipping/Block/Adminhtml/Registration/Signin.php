<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Block\Adminhtml\Registration;

use Magento\Backend\Block\Template\Context;
use Mageside\CanadaPostShipping\Helper\Carrier;
use Magento\Backend\Model\Auth\Session;

class Signin extends \Magento\Backend\Block\Template
{
    /**
     * @var Carrier
     */
    private $carrierHelper;

    /**
     * @var Session
     */
    private $session;

    /**
     * Signin constructor.
     * @param Context $context
     * @param Carrier $carrierHelper
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        Carrier $carrierHelper,
        Session $customerSession,
        array $data = []
    ) {
        $this->carrierHelper = $carrierHelper;
        $this->session = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getTokenId()
    {
        return $this->session->getData('canada_post_token_id');
    }

    /**
     * @return bool|mixed
     */
    public function getRegistrationMerchantUrl()
    {
        return ((bool) $this->carrierHelper->getConfigCarrier('sandbox_mode')) ?
            $this->carrierHelper->getConfigCarrier('registration_development_merchant_url') :
            $this->carrierHelper->getConfigCarrier('registration_merchant_url');
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        $websiteId = $this->getRequest()->getParam('website', 0);

        return $this->getUrl('canadapost/registration/returnAction', ['website' => $websiteId]);
    }

    /**
     * @return bool|mixed
     */
    public function getPlatformId()
    {
        return $this->carrierHelper->getConfigCarrier('platform_id');
    }
}
