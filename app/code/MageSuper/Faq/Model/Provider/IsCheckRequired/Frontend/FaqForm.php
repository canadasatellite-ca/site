<?php

namespace MageSuper\Faq\Model\Provider\IsCheckRequired\Frontend;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use MSP\ReCaptcha\Model\Config;
use MSP\ReCaptcha\Model\IsCheckRequired;
use Magento\Customer\Model\Session as CustomerSession;

class FaqForm extends IsCheckRequired
{
    private $_customerSession;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request,
        Config $config,
        CustomerSession $customerSession,
        $area = null,
        $enableConfigFlag = null,
        $requireRequestParam = null
    )
    {
        $this->_customerSession = $customerSession;
        parent::__construct($scopeConfig, $request, $config, $area, $enableConfigFlag, $requireRequestParam);
    }

    /**
     * Return true if check is required
     * @return bool
     */
    public function execute()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return false;
        }
        return parent::execute();
    }
}
