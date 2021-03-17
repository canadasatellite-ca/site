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

class Category extends \Magento\Framework\View\Element\Template
{
    public $categoryModel;
    public $faqResource;
    public $faqQuestionCollection;
    public $faqsHelper;
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magedelight\Faqs\Model\Category $categoryModel,
        \Magedelight\Faqs\Model\ResourceModel\Faq $faqResource,
        \Magedelight\Faqs\Helper\Data $faqsHelper,
        \Magedelight\Faqs\Model\ResourceModel\Faq\CollectionFactory $collection,
        \Magento\Framework\Filesystem\Io\File $systemFile,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->categoryModel = $categoryModel;
        $this->faqResource = $faqResource;
        $this->faqQuestionCollection = $collection;
        $this->faqsHelper = $faqsHelper;
        $this->fileSystem = $systemFile;
        $this->_filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $categoryModel = $this->LoadCategoryModel();
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
                'faqmain_page',
                [
                    'label' => $title,
                    'title' => $title,
                    'link'=> $faqUrl
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'category_page',
                [
                    'label' => $categoryModel->getTitle(),
                    'title' => $categoryModel->getTitle()
                ]
            );
        }
        $this->pageConfig->getTitle()->set($categoryModel->getPageTitle());
        $this->pageConfig->setKeywords($categoryModel->getMetaKeywords());
        $this->pageConfig->setDescription($categoryModel->getMetaDescription());
        $faqMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($faqMainTitle) {
            // Setting empty page title if content heading is absent
            $faqTitle = $categoryModel->getTitle() ? $categoryModel->getTitle(): ' ';
            $faqMainTitle->setPageTitle($this->escapeHtml($faqTitle));
        }
        return parent::_prepareLayout();
    }
    
    public function getQuestionCollection($categoryId, $storeId = null)
    {    
        if ($storeId === null) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        if ($categoryId) {
            $questionIds = $this->faqResource->getQuestionIds($categoryId);
            $collection = $this->faqQuestionCollection->create()
                ->addFieldToFilter('question_id', ['in'=>$questionIds])
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
                ->addFieldToFilter('answer', ['notnull' => true])
                ->addFieldToFilter('status', ['eq'=>\Magedelight\Faqs\Model\Faq::STATUS_ENABLED])
                ->setOrder('position', 'ASC');
        }
        return $collection;
    }
    
    public function LoadCategoryModel() {
        $categoryId = $this->getRequest()->getParam('category_id');
        return $this->categoryModel->load($categoryId);
    }
    public function getCategoriesData() {
        return $this->categoryModel->getCollection();
    }

    public function getjsonFilePath()
    {
        return $this->_storeManager->getStore()->getBaseMediaDir() . DIRECTORY_SEPARATOR .
                'md' . DIRECTORY_SEPARATOR . 'faq' . DIRECTORY_SEPARATOR .
               'store_' . $this->_storeManager->getStore()->getId() . '.txt';
    }
    
    public function getJsonData()
    {
        $data = '';
        
        $folder = $this->_storeManager->getStore()->getBaseMediaDir() . DIRECTORY_SEPARATOR . 'md' .
                DIRECTORY_SEPARATOR . 'faq' . DIRECTORY_SEPARATOR;
        if (!$this->fileSystem->fileExists($folder)) {
                $this->fileSystem->mkdir($folder, 0777, true);
        }
        $path = $this->getjsonFilePath();
        if ($this->fileSystem->fileExists($path)) {
            $data = $this->fileSystem->read($path);
        } else {
            $this->fileSystem->write($path, $data);
            $data = $this->fileSystem->read($path);
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
