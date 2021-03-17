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

class Faq extends \Magento\Framework\View\Element\Template
{
    
    const MD_FAQ_FAQ_GENERAL_TABS_JQUERY = 'md_faq/faq_general/faq_tabs_jquery';
    const MD_FAQ_FAQ_GENERAL_PER_PAGE_VALUE = 'md_faq/faq_general/faq_per_page_value';
    const MD_FAQ_CONFIG_LINK_URL_SUFFIX = 'md_faq/faq_general/faq_page_url_suffix';
    
    public $categoryCollection;
    public $faqQuestionCollection;
    public $systemFile;
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magedelight\Faqs\Model\ResourceModel\Category\CollectionFactory $faqCategoryCollection,
        \Magedelight\Faqs\Model\ResourceModel\Faq\CollectionFactory $faqQuestionCollection,
        \Magento\Framework\Filesystem\Io\File $systemFile,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->categoryCollection = $faqCategoryCollection;
        $this->faqQuestionCollection = $faqQuestionCollection;
        $this->fileSystem = $systemFile;
        $this->_filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }
     // @codingStandardsIgnoreStart
    protected function _prepareLayout()
    {
        $title = $this->_storeManager->getStore()->getConfig('md_faq/faq_general/faq_page_title');
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb('faq_page', ['label' => $title, 'title' => $title]);
        }
        $this->pageConfig->getTitle()->set($title);
        $this->pageConfig->setKeywords(
            $this->_storeManager->getStore()->getConfig('md_faq/faq_general/faq_meta_keywords')
        );
        $this->pageConfig->setDescription(
            $this->_storeManager->getStore()->getConfig('md_faq/faq_general/faq_meta_description')
        );
        $faqMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($faqMainTitle) {
            $faqTitle = $title ? $title : ' ';
            $faqMainTitle->setPageTitle($this->escapeHtml($faqTitle));
        }
        return parent::_prepareLayout();
    }
    // @codingStandardsIgnoreEnd
    public function getFaqCategories($storeId = null)
    {
        $storeId = ($storeId === null) ? $this->_storeManager->getStore()->getId() : $storeId;
        
        $faqCategoryCollection = $this->categoryCollection->create()
                ->addFieldToFilter(
                    ['store_id', 'store_id'],
                    [
                        ["finset"=>[$storeId]],
                        ["finset"=>[0]]
                    ]
                )
                ->addFieldToFilter('status', [
                    'eq' => \Magedelight\Faqs\Model\Category::STATUS_ENABLED
                    ])
                ->setOrder('sort_order', 'ASC');

        return $faqCategoryCollection;
    }

    public function getQuestionsByCategoryId($categoryId, $storeId = null)
    {
        $faqQuestionCollection = null;
        $storeId = ($storeId === null) ? $this->_storeManager->getStore()->getId() : $storeId;
        if ($categoryId) {
            $faqQuestionCollection = $this->faqQuestionCollection->create()
                    ->addFieldToFilter(
                        ['store_id', 'store_id'],
                        [
                            ["finset"=>[$storeId]],
                            ["finset"=>[0]]
                        ]
                    )
                    ->addFieldToFilter('is_active', [
                        'eq' => \Magedelight\Faqs\Model\Faq::STATUS_ENABLED
                        ])
                    ->addFieldToFilter('question_type', [
                        'in' => [\Magedelight\Faqs\Model\Faq::GENERIC_FAQ ,\Magedelight\Faqs\Model\Faq::BOTH_FAQ]
                        ])
                    ->addFieldToFilter('answer', ['notnull' => true])
                    ->addFieldToFilter('category_id', [
                        'eq' => $categoryId
                        ])
                    ->setOrder('sort_order', 'ASC');
        }
        return $faqQuestionCollection;
    }
    public function getAllQuestions($storeId = null)
    {
        $faqQuestionCollection = null;
        $storeId = ($storeId === null) ? $this->_storeManager->getStore()->getId() : $storeId;
        $faqQuestionCollection = $this->faqQuestionCollection->create()
            ->addFieldToFilter(
                ['store_id', 'store_id'],
                [
                    ["finset"=>[$storeId]],
                    ["finset"=>[0]]
                ]
            )
            ->addFieldToFilter('is_active', [
                'eq' => \Magedelight\Faqs\Model\Faq::STATUS_ENABLED
                ])
            ->addFieldToFilter('question_type', [
                'in' => [\Magedelight\Faqs\Model\Faq::GENERIC_FAQ ,\Magedelight\Faqs\Model\Faq::BOTH_FAQ]
                ])
            ->addFieldToFilter('answer', ['notnull' => true])
            ->setOrder('sort_order', 'ASC')
            ->setPageSize(
                $this->_storeManager->getStore()->getConfig(self::MD_FAQ_FAQ_GENERAL_PER_PAGE_VALUE)
            );
        return $faqQuestionCollection;
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
    
    public function getFaqSearchUrl()
    {
        return $this->getUrl('*/*/search');
    }
    
    public function isCategoryLinkable()
    {
        return $this->_storeManager->getStore()->getConfig(self::MD_FAQ_FAQ_GENERAL_TABS_JQUERY);
    }
    
    public function isCategoryUrl($categoryurlkey)
    {
        $categoryurl= $categoryurlkey.$this->_storeManager->getStore()->getConfig(self::MD_FAQ_CONFIG_LINK_URL_SUFFIX);
        return $categoryurl;
    }
    public function getAnswerContant($param) {
        $html = $this->_filterProvider->getPageFilter()->filter($param);
        return $html;
    }
}
