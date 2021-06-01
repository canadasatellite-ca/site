<?php

namespace CanadaSatellite\Theme\Controller\Customer;

use Magento\Framework\App\RequestInterface;

class Viewdevices extends \Magedelight\Firstdata\Controller\Firstdata
{
    protected function _getSession()
    {
        return $this->_customerSession;
    }

    function dispatch(RequestInterface $request)
    {
        if (!$this->_getSession()->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }

        return parent::dispatch($request);
    }
    function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}