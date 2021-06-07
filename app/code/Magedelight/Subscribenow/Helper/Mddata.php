<?php

/**
 * Magedelight
 * Copyright (C) 2017 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Mddata extends AbstractHelper
{

    protected $_curl;
    protected $_storeManager;
    protected $configWritter;
    protected $messageManager;
    protected $_cacheTypeList;
    protected $_cacheFrontendPool;
    protected $request;
    protected $_configInterface;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWritter,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface
    ) {
        $this->_curl = $curl;
        $this->messageManager = $messageManager;
        $this->configWritter = $configWritter;
        $this->_storeManager = $storeManager;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->request = $request;
        $this->_configInterface = $configInterface;

        parent::__construct($context);
    }

    public function getExtensionKey()
    {
        $extensionKey = "ek-magento2-subscribe-now";

        return $extensionKey;
    }

    public function getAllowedDomainsCollection()
    {
        $mappedDomains = [];
        $websites = [];
        $selected = [];
        $allWebsites = [];

        $url = $this->_storeManager->getStore()->getBaseUrl();
        $serial = $this->scopeConfig->getValue('md_subscribenow/license/serial_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $activation = $this->scopeConfig->getValue('md_subscribenow/license/activation_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $isEnabled = $this->scopeConfig->getValue('md_subscribenow/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $selectedWebsites = $this->scopeConfig->getValue('md_subscribenow/general/select_website', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (strpos($url, 'localhost') === false && strpos($url, '10.16.16.137') === false) {
            if ($serial == '' && $activation == '') {
                $this->messageManager->addError("Serial and Activation keys not found.Please enter valid keys for 'Subscribe Now' extension.");
            }
            if ($activation == '') {
                $this->messageManager->addError("Activation key not found.Please enter valid activation key for 'Subscribe Now' extension.");
            }

            if ($serial == '') {
                $this->messageManager->addError("Serial key not found.Please enter valid serial key for 'Subscribe Now' extension.");
            }

            $parsedUrl = parse_url($url);
            $domain = str_replace(['www.', 'http://', 'https://'], '', $parsedUrl['host']);
            $hash = $serial . '' . $domain;
            /* Curl post to check if key is valid * */
            $keys['sk'] = $serial;
            $keys['ak'] = $activation;
            $keys['domain'] = $domain;
            $keys['product_name'] = 'Subscribe Now';
            $keys['ek'] = $this->getExtensionKey();
            $keys['sw'] = $selectedWebsites;
            $field_string = http_build_query($keys);

            $curlPostUrl = 'https://www.magedelight.com/ktplsys/index/validate?' . $field_string; // live url

            try {
                $this->configWritter->save('md_subscribenow/license/data', 1);
                $this->_curl->post($curlPostUrl, $keys);
                $response = $this->_curl->getBody();
                $mappedDomains = json_decode($response);
                if (is_object(json_decode($response)) &&
                        null !== json_decode($response) &&
                        isset(get_object_vars($mappedDomains)['curl_success']) &&
                        get_object_vars($mappedDomains)['curl_success'] == 1
                ) {
                    $this->configWritter->save('md_subscribenow/license/data', 0);

                    if (is_object($mappedDomains)) {
                        $mappedDomains = get_object_vars($mappedDomains);
                    }

                    if ($isEnabled == 'No') {
                        $this->messageManager->addNotice($mappedDomains['msg']);
                    }

                    if (!isset($mappedDomains['domains'])) {
                        $this->configWritter->save('md_subscribenow/general/enabled', 0);
                    }

                    if (isset($mappedDomains['domains'])) {
                        $post = get_object_vars($this->request->getPost());
                        if (isset($post['groups']['general']['fields']['select_website']['value']) &&
                                !empty($post['groups']['general']['fields']['select_website']['value'])) {
                            $websites = $post['groups']['general']['fields']['select_website']['value'];
                            if (sizeof($websites) > 0 && !empty($websites[0])) {
                                $updateSelected = '';
                                if (count($websites) > 0) {
                                    foreach ($websites as $web) {
                                        $devPart = strchr($web, '.', true);
                                        $maindomain = str_replace($devPart . '.', '', $web);

                                        if (in_array($web, $mappedDomains['domains']) ||
                                                in_array($maindomain, $mappedDomains['domains'])
                                        ) {
                                            $selected[] = $web;
                                        }
                                    }
                                }
                                $updateSelected = implode(',', $selected);
                                $this->configWritter->save('md_subscribenow/general/select_website', $updateSelected);
                                if (empty($updateSelected)) {
                                    $this->configWritter->save('md_subscribenow/general/enabled', 0);
                                    $this->configWritter->save('md_subscribenow/license/data', 0);
                                    $this->configWritter->save('md_subscribenow/general/select_website', '');
                                }
                            }
                        } else {
                            if (!empty($selectedWebsites)) {
                                $websites = explode(',', $selectedWebsites);
                                foreach ($websites as $web2) {
                                    $devPart1 = strchr($web2, '.', true);
                                    $maindomain1 = str_replace($devPart1 . '.', '', $web2);

                                    if (in_array($web2, $mappedDomains['domains']) || in_array($maindomain1, $mappedDomains['domains'])) {
                                        $selected[] = $web2;
                                    }
                                }

                                $updateSelected2 = implode(',', $selected);
                                $this->configWritter->save('md_subscribenow/general/select_website', $updateSelected2);
                                if (empty($updateSelected2)) {
                                    $this->configWritter->save('md_subscribenow/general/enabled', 0);
                                    $this->configWritter->save('md_subscribenow/license/data', 0);
                                }
                            }
                        }

                        if (empty($mappedDomains['domains'])) {
                            $this->configWritter->save('md_subscribenow/license/data', 0);

                            $this->configWritter->save('md_subscribenow/general/enabled', 0);
                            $this->configWritter->save('md_subscribenow/license/serial_key', '');
                            $this->configWritter->save('md_subscribenow/license/activation_key', '');
                            $this->configWritter->save('md_subscribenow/general/select_website', '');

                            $this->messageManager->addError('Invalid activation and serial key for "Subscribe Now".');
                        } else {
                            $this->configWritter->save('md_subscribenow/license/data', 1);
                        }

                        $confWebsites = $this->_storeManager->getWebsites();
                        if (sizeof($confWebsites) > 0) {
                            foreach ($confWebsites as $website) {
                                foreach ($website->getStores() as $store) {
                                    $wedsiteId = $website->getId();
                                    $webUrl = $this->scopeConfig->getValue('web/unsecure/base_url', 'website', $website->getCode());
                                    $parsedUrl = parse_url($webUrl);
                                    $websiteUrl = str_replace(['www.', 'http://', 'https://'], '', $parsedUrl['host']);

                                    $devPart = strchr($websiteUrl, '.', true);
                                    $maindomain = str_replace($devPart . '.', '', $websiteUrl);

                                    $allWebsites[] = $websiteUrl;
                                    $allWebsites[] = $maindomain;

                                    if (!in_array($websiteUrl, $mappedDomains['domains']) &&
                                            !in_array($maindomain, $mappedDomains['domains'])) {
                                        $this->_configInterface
                                                ->saveConfig('md_subscribenow/general/enabled', 0, 'websites', $website->getId());
                                        $this->_configInterface
                                                ->saveConfig('md_subscribenow/general/enabled', 0, 'stores', $store->getId());
                                    }

                                    if ((in_array($websiteUrl, $selected) || in_array($maindomain, $selected))) {
                                        if (isset($post['groups']['general']['fields']['select_website'])) {
                                            if (isset($post['groups']['general']['fields']['enabled']['value'])) {
                                                if ($post['groups']['general']['fields']['enabled']['value']) {
                                                    $this->_configInterface
                                                            ->saveConfig('md_subscribenow/general/enabled', 1, 'websites', $website->getId());
                                                    $this->_configInterface
                                                            ->saveConfig('md_subscribenow/general/enabled', 1, 'stores', $store->getId());
                                                } else {
                                                    $this->_configInterface
                                                            ->saveConfig('md_subscribenow/general/enabled', 0, 'websites', $website->getId());
                                                    $this->_configInterface
                                                            ->saveConfig('md_subscribenow/general/enabled', 0, 'stores', $store->getId());
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $responseArray = [];
                        foreach ($allWebsites as $key => $domain) {
                            if (in_array($domain, $mappedDomains['domains'])) {
                                $responseArray[] = ['value' => $domain, "label" => $domain];
                            }
                        }

                        if (!sizeof($responseArray) && count($mappedDomains['domains']) > 0) {
                            $this->messageManager->addNotice('You didn\'t purchase license for domain(s) configured on this Magento setup');
                            $this->configWritter->save('md_subscribenow/general/enabled', 0);
                            $this->configWritter->save('md_subscribenow/license/data', 0);
                        }

                        if (count($responseArray) > 0 && !sizeof($selected)) {
                            $this->messageManager->addNotice('Please select website(s) to enable the extension.');
                            $this->configWritter->save('md_subscribenow/general/enabled', 0);
                            $this->configWritter->save('md_subscribenow/license/data', 0);
                        }
                    }
                }

                $types = ['config', 'full_page'];

                foreach ($types as $type) {
                    $this->_cacheTypeList->cleanType($type);
                }

                foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                    $cacheFrontend->getBackend()->clean();
                }
            } catch (\Exception $e) {
                $this->_logger->info($e->getMessage());
            }

            return $mappedDomains;
        }
    }
}
