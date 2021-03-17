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
namespace Magedelight\Faqs\Block;

class Search extends \Magento\Framework\View\Element\Template
{
    const MD_FAQ_CONFIG_LINK_URL_SUFFIX = 'md_faq/faq_general/faq_page_url_suffix';
    
    public $faqCollection;
    public $escaper;
    public $faqHelper;
    public $faqCategory;
    public $systemFile;
    protected $_filterProvider;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magedelight\Faqs\Model\ResourceModel\Faq\CollectionFactory $faqCollection,
        \Magedelight\Faqs\Model\Category $faqCategory,
        \Magento\Framework\Filesystem\Io\File $systemFile,
        \Magedelight\Faqs\Helper\Data $faqHelper,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->faqCollection = $faqCollection;
        $this->escaper = $context->getEscaper();
        $this->faqHelper = $faqHelper;
        $this->_filterProvider = $filterProvider;
        $this->systemFile = $systemFile;
        $this->faqCategory = $faqCategory;
    }
    // @codingStandardsIgnoreStart
    protected function _prepareLayout()
    {
        $title = $this->getSearchQueryText();

        $urlKey = $this->_storeManager->getStore()->getConfig('md_faq/faq_general/faq_page_url_key');
        $urlSuffix = $this->_storeManager->getStore()->getConfig('md_faq/faq_general/faq_page_url_suffix');
        $faqUrlroute = $this->_storeManager->getStore()->getConfig('md_faq/faq_general/faq_page_route');
        $faqUrlroute = $faqUrlroute ? $faqUrlroute : 'faqs';

        $faqUrl = $this->_storeManager->getStore()->getBaseUrl().$faqUrlroute;
        
        //$faqUrl .= ($urlSuffix != '') ? $urlSuffix: '/';
        
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            )->addCrumb(
                'faq_main',
                [
                    'label' => $this->faqHelper->getFaqTitle(),
                    'title' => $this->faqHelper->getFaqTitle(),
                    'link' =>  $faqUrl
                ]
            )->addCrumb(
                'search',
                ['label' => $title, 'title' => $title]
            );
        }
        return parent::_prepareLayout();
    }
    // @codingStandardsIgnoreEnd
    public function getSearchResult($storeId = null)
    {
        $result = [];
        if ($storeId === null) {
            $storeId = $this->_storeManager->getStore()->getId();
        }

        if ($this->getRequest()->getParam('sqr')!='') {
            $collection = $this->faqCollection->create()
                ->addFieldToFilter('question_type', [
                    'in' => [\Magedelight\Faqs\Model\Faq::GENERIC_FAQ ,\Magedelight\Faqs\Model\Faq::BOTH_FAQ]
                    ])
                ->addFieldToFilter(
                    ['store_id', 'store_id'],
                    [
                        ["finset"=>[$storeId]],
                        ["finset"=>[0]]
                    ]
                )
                ->addFieldToFilter('status', [
                    'eq' => \Magedelight\Faqs\Model\Faq::STATUS_ENABLED
                    ])
                ->addFieldToFilter(
                    ['question', 'answer'],
                    [
                        ['like'=>'%'.$this->getRequest()->getParam('sqr').'%'],
                        ['like'=>'%'.$this->getRequest()->getParam('sqr').'%']
                    ]
                );
            if ($collection->getSize()) {
                foreach ($collection as $_questions) {
                    $result[] = $_questions;
                }
            }
        }
        return $result;
    }
    
    public function getSearchQueryText()
    {
        return __("Search results for: '%1'", $this->escaper->escapeHtml($this->getRequest()->getParam('sqr')));
    }
    public function getSearchValue() {
        return $this->getRequest()->getParam('sqr');
    }
    
    public function getFaqCategory($categoryId)
    {
        $model = null;
        if ($categoryId) {
            $model = $this->faqCategory->load($categoryId);
        }
        return $model;
    }
    
    public function getCategoryUrl($categoryurlkey)
    {
        $categoryurl =  $categoryurlkey.$this->_storeManager->getStore()
                        ->getConfig(self::MD_FAQ_CONFIG_LINK_URL_SUFFIX);
         
        return $this->getUrl('', ['_direct'=>'faqs/'.$categoryurl]);
    }
    
    public function getjsonFilePath()
    {
        return  $this->_storeManager->getStore()->getBaseMediaDir() . DIRECTORY_SEPARATOR . 'md'
                . DIRECTORY_SEPARATOR . 'faq' . DIRECTORY_SEPARATOR . 'store_' .
                $this->_storeManager->getStore()->getId() . '.txt';
    }
    
    public function getJsonData()
    {
        $data = '';
        $folder = $this->_storeManager->getStore()->getBaseMediaDir() . DIRECTORY_SEPARATOR
                . 'md' . DIRECTORY_SEPARATOR . 'faq' . DIRECTORY_SEPARATOR;
        if (!$this->systemFile->fileExists($folder)) {
                $this->systemFile->mkdir($folder, 0777, true);
        }
        $path = $this->getjsonFilePath();
        if ($this->systemFile->fileExists($path)) {
            $data = $this->systemFile->read($path);
        } else {
            $this->systemFile->write($path, $data);
            $data = $this->systemFile->read($path);
        }
        if ($data == '') {
            return "null";
        } else {
            return $data;
        }
    }
    public function getAnswerContant($param) {
        $html = $this->_filterProvider->getPageFilter()->filter($param);
        return $html;
    }
}
