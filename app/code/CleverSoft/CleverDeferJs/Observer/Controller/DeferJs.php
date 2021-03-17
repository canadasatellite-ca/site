<?php
/**
 * @category    CleverSoft
 * @package     CleverDeferJs
 * @copyright   Copyright © 2017 CleverSoft., JSC. All Rights Reserved.
 * @author      ZooExtension.com
 * @email       magento.cleversoft@gmail.com
 */
namespace CleverSoft\CleverDeferJs\Observer\Controller;

class DeferJs implements \Magento\Framework\Event\ObserverInterface
{
    protected $helper;

    public function __construct(
        \CleverSoft\CleverDeferJs\Helper\Data $data,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    ){
        $this->helper = $data;
        $this->_urlInterface = $urlInterface;
        $this->_request = $request;
        $this->_cacheFrontendPool = $cacheFrontendPool;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        if($this->helper->getConfig('cleverdeferjs/general/enable')) {
            $currentUrl = $this->_urlInterface->getCurrentUrl();
            $baseUrl = $this->_urlInterface->getBaseUrl();
            $currentPath = substr($currentUrl,strlen($baseUrl),strlen($currentUrl));
            /*
            foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
                break;
            }
            */
            
            $actionName = $this->_request->getFullActionName();

            $listExcludeActionName = json_decode($this->helper->getConfig('cleverdeferjs/general/exclude_controllers'),true);
            if (isset($excludePath) && in_array($currentPath, $excludePath)) {
                return;
            }
            if (isset($listExcludeActionName) && in_array($actionName, $listExcludeActionName)) {
                return;
            }
            if ($actionName == 'cms_index_index' && $this->helper->getConfig('cleverdeferjs/general/exclude_homepage')) {
                return;
            }
            $response = $observer->getEvent()->getResponse();
            if (!$response) return;

            $html = $response->getBody();
            if (stripos($html, "</body>") === false) return;

            preg_match_all('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', $html, $scripts);
            if ($scripts and isset($scripts[0]) and $scripts[0]) {
                $html = preg_replace('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', '', $response->getBody());
                $scripts = implode("", $scripts[0]);
                $html = str_ireplace("</body>", "$scripts</body>", $html);
                $response->setBody($html);
            }
        }
    }
}
