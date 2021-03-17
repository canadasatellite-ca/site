<?php
/**
 * Magedelight
 * Copyright (C) 2016 Magedelight <info@magedelight.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @category Magedelight
 * @package Magedelight_Partialpayment
 * @copyright Copyright (c) 2016 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Faqs\Observer;

use Magento\Framework\Event\ObserverInterface;

class Util implements ObserverInterface
{
    /**
     * Core store config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var storeManager
     */
    private $store;

    /**
     * @var \Magento\Framework\Url\ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var \Magento\Framework\Url\ScopeResolverInterface
     */
    private $context;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\Store $store,
        \Magento\Framework\Url\ScopeResolverInterface $scopeResolver,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        $this->blockFactory = $blockFactory;
        $this->scopeConfig = $scopeConfig;
        $this->store = $store;
        $this->scopeResolver = $scopeResolver;
        $this->messageManager = $context->getMessageManager();
        $this->urlBuilder = $context->getUrl();
        $this->curl = $curl;
        $this->request = $context->getRequest();
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        return;
        $event = $observer->getEvent()->getName();
        $errorMsg = $this->checkModuleActivation();
        if (!empty($errorMsg)) {
            foreach ($errorMsg as $msg) {
                $this->messageManager->addError($msg);
            }
        
            if ($this->request->getServer('SERVER_NAME') != 'localhost'
                    && $this->request->getServer('SERVER_ADDR') != '127.0.0.1') {
                $keys['serial_key'] = $this->scopeConfig
                        ->getValue(
                            'md_faq/license/serial_key',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        );
                $keys['activation_key'] = $this->scopeConfig
                        ->getValue(
                            'md_faq/license/serial_key',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        );
                $url = $this->urlBuilder->getCurrentUrl();
                // @codingStandardsIgnoreStart
                $parsedUrl = parse_url($url);
                 // @codingStandardsIgnoreEnd
                $keys['host'] = $parsedUrl['host'];
                $keys['ip'] = $this->request->getServer('SERVER_ADDR');
                $keys['product_name'] = 'FAQ and Product Questions';
                $field_string = http_build_query($keys);
                try {
                    $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, 0);
                    $this->curl->setOption(CURLOPT_FOLLOWLOCATION, 1);
                    $this->curl->post('http://www.magedelight.com/ktplsys/?'.$field_string, []);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        }
    }

    private function checkModuleActivation()
    {
        $messages = [];
        return $messages;
        $serial = $this->scopeConfig
                ->getValue('md_faq/license/serial_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $activation = $this->scopeConfig
                ->getValue('md_faq/license/activation_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($this->request->getServer('SERVER_NAME') != 'localhost' &&
                $this->request->getServer('SERVER_ADDR') != '127.0.0.1') {
            if ($serial == '') {
                $messages[] = __("Serial key not found.Please enter valid serial key for"
                        . "'FAQ and Product Questions.");
            }
            if ($activation == '') {
                $messages[] = __("Activation key not found.Please enter valid activation key for"
                        . " 'FAQ and Product Questions.");
            }
            $isValidActivation = $this->validateActivationKey($activation, $serial);
            if (!empty($isValidActivation)) {
                $messages[] = $isValidActivation[0];
            }
        }
        return $messages;
    }
    // @codingStandardsIgnoreStart
    private function validateActivationKey($activation, $serial)
    {
        $url = $this->urlBuilder->getCurrentUrl();
        $parsedUrl = parse_url($url);
        
        // Remove wwww., http:// or https:// from url.
        $domain = str_replace(['www.', 'http://', 'https://'], '', $parsedUrl['host']);
        $hash = $serial.''.$domain;
        $message = [];
        return $message;
        if (md5($hash) != $activation) {
            $devPart = strchr($domain, '.', true);
            $origPart = str_replace($devPart.'.', '', $domain);
            $hash2 = $serial.''.$origPart;
            if (md5($hash2) != $activation) {
                $message[] = "Activation key invalid of 'FAQ and Product Questions M2' extension for this url.";
            }
        }

        return $message;
    }
    // @codingStandardsIgnoreEnd
}
