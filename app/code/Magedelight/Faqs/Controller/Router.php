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

namespace Magedelight\Faqs\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{

    public $actionFactory;
    public $eventManager;
    public $storeManager;
    public $pageFactory;
    public $appState;
    public $url;
    public $response;
    public $faqCategoryCollection;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response,
        \Magedelight\Faqs\Model\ResourceModel\Category\CollectionFactory $collectionFactory
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->url = $url;
        $this->pageFactory = $pageFactory;
        $this->storeManager = $storeManager;
        $this->response = $response;
        $this->faqCategoryCollection = $collectionFactory;
    }
    // @codingStandardsIgnoreStart
    public function match(\Magento\Framework\App\RequestInterface $request)
    {   // @codingStandardsIgnoreEnd
        $storeId = $this->storeManager->getStore()->getId();
        $isFrontendEnabled = (boolean) $this->storeManager->getStore()->getConfig('md_faq/general/enabled_faq');
        if (!$isFrontendEnabled) {
            return false;
        }
        $faqUrlkey = $this->storeManager->getStore()->getConfig('md_faq/faq_general/faq_page_url_key');
        $faqUrlroute = $this->storeManager->getStore()->getConfig('md_faq/faq_general/faq_page_route');
        $faqUrlroute = $faqUrlroute ? $faqUrlroute : 'faqs';
        $faqUrlPrefix = $this->storeManager->getStore()->getConfig('md_faq/faq_general/faq_page_url_suffix');
        $faqUrlPrefix = $faqUrlPrefix ? $faqUrlPrefix : '.html';
        $identifier1 = trim($request->getPathInfo(), '/');
                    if ($identifier1 == $faqUrlroute) {
                        $request->setModuleName('faqs')->setControllerName('index')->setActionName('index');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier1);
return $this->actionFactory->create(
                'Magento\Framework\App\Action\Forward',
                ['request' => $request]
            );
        } else {

        $identifier = str_replace("/".$faqUrlroute."/", "", $request->getPathInfo());
        


        $replaceBaseUrl = trim($faqUrlkey, '/');
        $replaceBaseUrl .= (strlen($faqUrlPrefix) > 0 || $faqUrlPrefix != '') ?
                '.' . str_replace('.', '', $faqUrlPrefix) : '/';
        $replaceBaseUrl = trim($replaceBaseUrl, "/");
        if ($identifier !== $replaceBaseUrl) {
            $suffixReplaced = str_replace([$faqUrlPrefix], '', $identifier);
            $suffixReplaced = trim($suffixReplaced, '/');
            $filterCollection = $this->faqCategoryCollection->create()
                    ->addFieldToFilter(
                        ['store_id', 'store_id'],
                        [
                        ["finset" => [$storeId]],
                        ["finset" => [0]]
                            ]
                    )
                    ->addFieldToFilter('url_key', ['eq' => $suffixReplaced])
                    ->addFieldToFilter('status', ['eq' => \Magedelight\Faqs\Model\Category::STATUS_ENABLED]);
            if ($filterCollection->getSize() > 0) {
                // @codingStandardsIgnoreStart
                $firstItem = $filterCollection->getFirstItem();
                // @codingStandardsIgnoreEnd
                $request->setModuleName('faqs')
                        ->setControllerName('index')
                        ->setActionName('category')
                        ->setParam('category_id', $firstItem->getId());
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                );
            } else {
                return false;
            }
        } else {
            $request->setModuleName('faqs')->setControllerName('index')->setActionName('index');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Forward',
                ['request' => $request]
            );
        }
        
    }
    }
}
