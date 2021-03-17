<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Controller\Postoffice;

class GetDetail extends \Mageside\CanadaPostShipping\Controller\Postoffice\GetList
{
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('/');
        }

        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(__('Invalid form key.'));
            return $this->getResult(['error' => true]);
        }

        $officeId = $this->getRequest()->getParam('office_id');
        if (!$officeId) {
            $this->messageManager->addErrorMessage(__('Invalid post code.'));
            return $this->getResult(['error' => true]);
        }

        /** @var \Mageside\CanadaPostShipping\Model\Service\Postoffice $service */
        $service = $this->_postOfficeServiceFactory->create();
        $office = $service->getPostOfficeDetail($officeId);

        return $this->getResult($office);
    }
}
