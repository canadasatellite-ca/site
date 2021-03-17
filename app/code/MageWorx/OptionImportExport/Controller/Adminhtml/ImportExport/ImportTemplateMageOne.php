<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionImportExport\Controller\Adminhtml\ImportExport;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use MageWorx\OptionImportExport\Model\MageOne\ImportTemplateHandler as MageOneImportTemplateHandler;
use Psr\Log\LoggerInterface as Logger;
use Magento\Backend\Model\Session as BackendSession;

class ImportTemplateMageOne extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageWorx_OptionImportExport::import_export';

    /**
     * @var MageOneImportTemplateHandler
     */
    protected $importTemplateHandler;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @param Context $context
     * @param MageOneImportTemplateHandler $importTemplateHandler
     * @param Logger $logger
     * @param BackendSession $backendSession
     */
    public function __construct(
        Context $context,
        Logger $logger,
        BackendSession $backendSession,
        MageOneImportTemplateHandler $importTemplateHandler
    ) {
        $this->importTemplateHandler = $importTemplateHandler;
        $this->logger                = $logger;
        $this->backendSession        = $backendSession;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {

            $file = $this->getRequest()->getFiles('mageworx_optiontemplates_import_magento_one_file');
            $map  = $this->getRequest()->getParams();

            if ($file && !empty($file['tmp_name'])) {

                $this->backendSession->setFileMagentoVersion('1');
                try {
                    $this->importTemplateHandler->importFromFile($file, $map);
                    $this->clearTempVariables();
                    $this->messageManager->addSuccessMessage(__('The option templates have been imported.'));
                    $this->processMissingImageFiles();
                } catch (\Magento\Framework\Exception\IntegrationException $e) {
                    $this->addPossibleSystemDataMismatchMessage($e->getMessage());
                } catch (\Magento\Framework\Exception\FileSystemException $e) {
                    $this->addMissingImagesMessage($e->getMessage());
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                    $this->addImportErrorMessage();
                } finally {
                    $this->backendSession->setStoreIds($this->importTemplateHandler->getStoreIds());
                    $this->backendSession->setCustomerGroupIds($this->importTemplateHandler->getCustomerGroupIds());
                }
            } else {
                $this->addInvalidFileMessage();
            }

        } else {
            $this->addInvalidFileMessage();
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }

    /**
     * @return void
     */
    protected function addInvalidFileMessage()
    {
        $this->messageManager->addErrorMessage(__('Invalid file upload attempt'));
    }

    /**
     * @return void
     */
    protected function addImportErrorMessage()
    {
        $this->messageManager->addErrorMessage(__('Something goes wrong while templates import'));
    }

    /**
     * @param string $message
     * @return void
     */
    protected function addMissingImagesMessage($message)
    {
        $this->messageManager->addErrorMessage(
            $message
        );
        $this->messageManager->addErrorMessage(
            __(
                "Please, transfer Magento %1 MageWorx Advanced Product Options media folder %2 first or turn on 'Ignore missing images' setting in module configuration",
                '1',
                '(media/customoptions/)'
            )
        );
    }

    /**
     * @param string $message
     * @return void
     */
    protected function addPossibleSystemDataMismatchMessage($message)
    {
        $this->messageManager->addWarningMessage($message);
    }

    /**
     * @return void
     */
    protected function processMissingImageFiles()
    {
        $missingImages = $this->importTemplateHandler->getMissingImagesList();
        if ($missingImages) {
            $this->addMissingImagesListInLogMessage();
            foreach ($missingImages as $missingImage) {
                $this->logger->warning(__('Missing MageWorx image file') . ': pub/media/' . $missingImage);
            }
        }
    }

    /**
     * @return void
     */
    protected function addMissingImagesListInLogMessage()
    {
        $this->messageManager->addWarningMessage(
            __("You can find list of missing MageWorx image files in") . ' ' . 'var/log/system.log'
        );
    }

    /**
     * @return void
     */
    protected function clearTempVariables()
    {
        $this->backendSession->setStoreIds([]);
        $this->backendSession->setCustomerGroupIds([]);
    }
}
