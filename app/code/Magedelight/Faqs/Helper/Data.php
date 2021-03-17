<?php
/* Magedelight
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
 * @package Magedelight_Faqs
 * @copyright Copyright (c) 2016 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 * 
 */
namespace Magedelight\Faqs\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MD_FAQ_CONFIG_ENABLED = 'md_faq/general/enabled_faq';
    const MD_FAQ_CONFIG_PAGE_TITLE = 'md_faq/faq_general/faq_page_title';
    const MD_FAQ_CONFIG_LINK_TITLE = 'md_faq/faq_general/faq_link_title';
    const MD_FAQ_CONFIG_LINK_URL_KEY = 'md_faq/faq_general/faq_page_url_key';
    const MD_FAQ_CONFIG_LINK_route = 'md_faq/faq_general/faq_page_route';
    const MD_FAQ_CONFIG_LINK_URL_SUFFIX = 'md_faq/faq_general/faq_page_url_suffix';
    const MD_FAQ_CONFIG_METAKEYWORDS = 'md_faq/faq_general/faq_meta_keywords';
    const MD_FAQ_CONFIG_METADESCRIPTION = 'md_faq/faq_general/faq_meta_description';
    
    public $storeManager;
    public $customerSession;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }
    
    public function getFaqTitle()
    {
        return $this->storeManager->getStore()->getConfig(self::MD_FAQ_CONFIG_LINK_TITLE);
    }
    
    public function getFaqUrl()
    {   
        $faqUrlroute = $this->getFaqsRoute();
        $urlKey = $this->storeManager->getStore()->getConfig(self::MD_FAQ_CONFIG_LINK_URL_KEY);
        $urlSuffix = $this->storeManager->getStore()->getConfig(self::MD_FAQ_CONFIG_LINK_URL_SUFFIX);
        $urlKey .= (strlen($urlSuffix) > 0 || $urlSuffix != '') ? '.'.str_replace('.', '', $urlSuffix): '/';
        return $this->storeManager->getStore()->getBaseUrl().$faqUrlroute.'/'.$urlKey;
    }
    public function getConfig($config_path)
    {
         return $this->storeManager->getStore()->getConfig($config_path);
    }
    public function getCustomerGroup() {
        if($this->customerSession->isLoggedIn()) {
            $groups = $this->customerSession->getCustomer()->getGroupId();
            return $groups;
        }
        return '0';
    }

    public function getFaqsRoute() {
        
        $route = $this->getConfig(self::MD_FAQ_CONFIG_LINK_route); 

        return $route ? $route : 'faqs';
    }
}
