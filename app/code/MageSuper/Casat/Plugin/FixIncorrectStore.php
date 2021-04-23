<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageSuper\Casat\Plugin;

use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Phrase;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Api\StoreResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ContextPlugin
 */
class FixIncorrectStore
{
    protected $httpRequest;
    function __construct(
        \Magento\Framework\App\Request\Http $httpRequest
    ) {
        $this->httpRequest  = $httpRequest;
    }
    /**
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param callable $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    function beforeDispatch(
        \Magento\Framework\App\FrontControllerInterface $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $store = $this->httpRequest->getParam('___store');
        $Fromstore = $this->httpRequest->getParam('___from_store');
        if ($store && $store=='default'){
            $this->httpRequest->setParam('___store','en');
        }
        if ($store && $store=='french'){
            $this->httpRequest->setParam('___store','fr');
        }
        if ($store && $Fromstore=='french'){
            $this->httpRequest->setParam('___from_store','fr');
        }
    }
}
