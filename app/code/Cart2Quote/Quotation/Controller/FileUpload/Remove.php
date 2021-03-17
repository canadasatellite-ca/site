<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\FileUpload;

/**
 * Class Remove
 * @package Cart2Quote\Quotation\Controller\FileUpload
 */
class Remove extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\File
     */
    protected $fileModel;

    /**
     * Remove constructor.
     * @param \Cart2Quote\Quotation\Model\Quote\File $fileModel
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\File $fileModel,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->fileModel = $fileModel;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $fileName = $this->getRequest()->getParam('file');
            $this->fileModel->removeFile($fileName);
            $this->messageManager->addSuccessMessage(__('File %1 removed', $fileName));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $defaultUrl = $this->_url->getUrl('*/*');

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl($defaultUrl));
    }
}
