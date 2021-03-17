<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Controller\Adminhtml\Manifest;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Framework\App\Response\Http\FileFactory;
use Mageside\CanadaPostShipping\Model\ResourceModel\Manifest\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Mageside\CanadaPostShipping\Model\Service\Manifest;
use Mageside\CanadaPostShipping\Model\Service\Transmit;

class MassPrintManifests extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mageside_CanadaPostShipping::mageside_canadapost_shipping';

    /**
     * @var LabelGenerator
     */
    private $labelGenerator;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private $filter;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var object
     */
    private $collectionFactory;

    /**
     * @var \Mageside\CanadaPostShipping\Model\Service\Manifest
     */
    private $manifestService;

    /**
     * MassPrintManifests constructor.
     * @param Context $context
     * @param Filter $filter
     * @param FileFactory $fileFactory
     * @param LabelGenerator $labelGenerator
     * @param CollectionFactory $collectionFactory
     * @param Manifest $manifestService
     */
    public function __construct(
        Context $context,
        Filter $filter,
        FileFactory $fileFactory,
        LabelGenerator $labelGenerator,
        CollectionFactory $collectionFactory,
        Manifest $manifestService
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->fileFactory = $fileFactory;
        $this->labelGenerator = $labelGenerator;
        $this->manifestService = $manifestService;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|ResponseInterface
     */
    public function execute()
    {
        try {
            /** @var AbstractCollection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            return $resultRedirect->setPath('canadapost/manifest/index');
        }
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect|ResponseInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        $labelsContent = [];
        $notTransmitted = [];

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($collection->getSize()) {
            /** @var \Mageside\CanadaPostShipping\Model\Manifest $manifest */
            $manifests = $collection->getItems();
            foreach ($manifests as $manifest) {
                if ($manifest->getStatus() == Transmit::STATUS_PENDING) {
                    $notTransmitted[] = $manifest->getId();
                }
            }

            if (!empty($notTransmitted)) {
                $this->messageManager->addErrorMessage(
                    __('You must transmit following manifests before printing them. IDs: %1', implode(', ', $notTransmitted))
                );
                return $resultRedirect->setPath('canadapost/manifest/index');
            }

            reset($manifests);
            foreach ($manifests as $manifest) {
                if ($manifest->getStatus() == Transmit::STATUS_TRANSMITTED_OFFLINE) {
                    continue;
                }
                $response = $this->manifestService->getManifestPrint($manifest);
                if (isset($response['manifest'])) {
                    $labelsContent[] = $response['manifest'];
                }
            }
        }

        if (!empty($labelsContent)) {
            $outputPdf = $this->labelGenerator->combineLabelsPdf($labelsContent);
            return $this->fileFactory->create(
                'Manifests.pdf',
                $outputPdf->render(),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }

        $this->messageManager->addErrorMessage(__('There are no manifests to selected shipments.'));
        return $resultRedirect->setPath('canadapost/manifest/index');
    }
}
