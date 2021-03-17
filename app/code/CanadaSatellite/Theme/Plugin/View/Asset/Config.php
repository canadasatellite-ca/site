<?php

namespace CanadaSatellite\Theme\Plugin\View\Asset;

use Magento\Framework\View\Asset\Config as ParentConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Request\Http;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var State
     */
    protected $_state;

    /**
     * @var Http
     */
    protected $request;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param State $state
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        State $state,
        Http $request
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->_state = $state;
        $this->request = $request;
    }

    public function aroundIsBundlingJsFiles(ParentConfig $subject, callable $proceed)
    {
        if ($this->request->getFullActionName()=='checkout_index_index'){
            return 0;
        }
        return (bool)$this->scopeConfig->isSetFlag(
            ParentConfig::XML_PATH_JS_BUNDLING,
            'adminhtml' === $this->_state->getAreaCode() ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_STORE
        );
    }
}