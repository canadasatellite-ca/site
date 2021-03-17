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
namespace Magedelight\Faqs\Block\Question;

class View extends \Magento\Framework\View\Element\Template
{
    
    const MD_FAQ_FAQ_GENERAL_TABS_JQUERY = 'md_faq/faq_general/faq_tabs_jquery';
    const MD_FAQ_FAQ_GENERAL_PER_PAGE_VALUE = 'md_faq/faq_general/faq_per_page_value';
    const MD_FAQ_CONFIG_LINK_URL_SUFFIX = 'md_faq/faq_general/faq_page_url_suffix';
    
    public $faqModel;
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magedelight\Faqs\Model\Faq $faqModel,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->faqModel = $faqModel;
        $this->_filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }
    
    protected function _prepareLayout()
    {
        $questionData = $this->getQuestionData();
        $title = $this->_storeManager->getStore()->getConfig('md_faq/faq_general/faq_page_title');
        $urlKey = $this->_storeManager->getStore()->getConfig('md_faq/faq_general/faq_page_url_key');
        $urlSuffix = $this->_storeManager->getStore()->getConfig('md_faq/faq_general/faq_page_url_suffix');
        $faqUrlroute = $this->_storeManager->getStore()->getConfig('md_faq/faq_general/faq_page_route');
        $faqUrlroute = $faqUrlroute ? $faqUrlroute : 'faqs';
        $faqUrl = $this->_storeManager->getStore()->getBaseUrl().$faqUrlroute;
        
        //$faqUrl .= ($urlSuffix != '') ? $urlSuffix: '/';
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'faq_page',
                [
                    'label' => $title,
                    'title' => $title,
                    'link'=> $faqUrl
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'faq_view',
                [
                    'label' => $questionData->getQuestion(),
                    'title' => $questionData->getQuestion()
                ]
            );
        }
        $this->pageConfig->getTitle()->set($questionData->getPageTitle());
        $this->pageConfig->setKeywords($questionData->getMetaKeywords());
        $this->pageConfig->setDescription($questionData->getMetaDescription());
        
        return parent::_prepareLayout();
    }
    
    public function getQuestionData() {
        $questionId = $this->getRequest()->getParam('id');
        return $this->faqModel->load($questionId);
    }
    
    public function getAutherInfoVisible() {
        return $this->_storeManager->getStore()->getConfig('md_faq/faq_general/authorinfo');
    }
    
    public function getSocialLinksVisible() {
        return $this->_storeManager->getStore()->getConfig('md_faq/faq_general/sociallinks');
    }
    public function getAnswerContant($param) {
        $html = $this->_filterProvider->getPageFilter()->filter($param);
        return $html;
    }
}
