<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class Index extends Template
{
    /**
     * Config path used for frontend url
     */
    private const FRONTEND_URL_PATH = 'channel/amazon/frontend_url';

    /**
     * Local scope config variable
     */
    private $scopeConfig;
    /**
     * @var \Magento\Amazon\Model\ModuleVersionResolver
     */
    private $moduleVersionResolver;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Amazon\Model\ModuleVersionResolver $moduleVersionResolver
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
        $this->moduleVersionResolver = $moduleVersionResolver;
    }

    /**
     * Returns config for frontend url
     * @return string
     */
    public function getFrontendUrl(): string
    {
        $url = (string)$this->scopeConfig->getValue(
            self::FRONTEND_URL_PATH,
            ScopeInterface::SCOPE_WEBSITE
        );
        if (!$url) {
            $url = sprintf(
                'https://cdn.amazon.channels.magento.com/%s/loader.js',
                $this->moduleVersionResolver->getVersion()
            );
        }
        return $url;
    }

    public function getGraphqlEndpoint(): string
    {
        return $this->_urlBuilder->getUrl('channel/amazon/graphql');
    }
}
