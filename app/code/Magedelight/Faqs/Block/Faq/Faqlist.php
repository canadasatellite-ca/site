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

namespace Magedelight\Faqs\Block\Faq;

use Magento\Customer\Model\Session;

class Faqlist extends \Magento\Framework\View\Element\Template
{
 
    const MD_FAQ_GENERAL_PRODUCT_GUEST_ENABLED = 'md_faq/general_product/enabled_product_guest';
    
    public $registry;
    public $faqList;
    public $faqFactory;
    /**
     * @var Session
     */
    public $session;
    public $storeManager;
    public $httpContext;
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Session $customerSession,
        \Magedelight\Faqs\Model\FaqFactory $faqFactory,
        \Magedelight\Faqs\Model\Faq $faqList,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->faqList = $faqList;
        $this->session = $customerSession;
        $this->faqFactory = $faqFactory;
        $this->storeManager = $storeManager;
        $this->httpContext = $httpContext;
        $this->_filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }
    public function getFormAction()
    {
        
        return $this->getUrl('faqs/faq/addquetion');
    }
    public function getFormCheckAction()
    {
       
        return $this->getUrl('faqs/faq/formauthcheck');
    }
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getFaqIds()
    {
        $productId = $this->getCurrentProduct()->getId();
        return $this->faqList->getFaqIds($productId);
    }

    public function getFaqList($storeId = null)
    {   
        if ($storeId === null) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        $faqIds = $this->getFaqIds();
        $collection = $this->faqFactory->create()->getCollection();
        $collection->addFieldToFilter('question_id', ['in' => $faqIds]);
        $collection->addFieldToFilter(
                    ['store_id', 'store_id'],
                    [
                        ["finset"=>[$storeId]],
                        ["finset"=>[0]]
                    ]
                );
        $collection->addFieldToFilter('status', '1');
        $collection->addFieldToFilter('answer', ['notnull' => true]);
        $collection->addFieldToFilter(
            'question_type',
            ['in' => [\Magedelight\Faqs\Model\Faq::PRODUCT_FAQ ,\Magedelight\Faqs\Model\Faq::BOTH_FAQ]]
        );
        return $collection;
    }
    public function isAllowCustomer()
    {
        $isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        return $isLoggedIn;
    }
    public function isAllowGuestCustomer()
    {
        return $this->storeManager->getStore()->getConfig(self::MD_FAQ_GENERAL_PRODUCT_GUEST_ENABLED);
    }
    
    public function getAnswerContant($param) {
        $html = $this->_filterProvider->getPageFilter()->filter($param);
        return $html;
    }
}
