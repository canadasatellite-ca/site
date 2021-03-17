<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Controller\Adminhtml\Manifest;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class PrintManifest extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mageside_CanadaPostShipping::mageside_canadapost_shipping';

    /**
     * @var \Mageside\CanadaPostShipping\Model\Manifest
     */
    private $_manifest;

    /**
     * @var \Mageside\CanadaPostShipping\Model\Service\Manifest
     */
    private $_manifestService;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $_fileFactory;

    /**
     * PrintManifest constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mageside\CanadaPostShipping\Model\Manifest $manifest
     * @param \Mageside\CanadaPostShipping\Model\Service\Manifest $manifestService
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageside\CanadaPostShipping\Model\Manifest $manifest,
        \Mageside\CanadaPostShipping\Model\Service\Manifest $manifestService,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_manifest = $manifest;
        $this->_manifestService = $manifestService;
        $this->_fileFactory = $fileFactory;

        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect||\Magento\Framework\App\Response\Http\File
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $manifestId = $this->getRequest()->getParam('manifest_id');
        if (!$manifestId) {
            $this->messageManager->addErrorMessage(__('Can\'t find manifest.'));

            return $resultRedirect->setPath('canadapost/manifest/index');
        }

        try {
            $manifest = $this->_manifest->load($manifestId);
            $response = $this->_manifestService->getManifestPrint($manifest);
            if (isset($response['manifest'])) {
                return $this->_fileFactory->create(
                    'Manifest(' . $manifest->getId() . ').pdf',
                    $response['manifest'],
                    DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            } elseif (isset($response['messages'])) {
                $this->messageManager->addErrorMessage($response['messages']);
            } else {
                $this->messageManager->addErrorMessage(__('Something went wrong while printing manifest.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('canadapost/manifest/index');
    }
}
