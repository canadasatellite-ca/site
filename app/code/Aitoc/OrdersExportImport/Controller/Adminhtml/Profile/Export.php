<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action;
use Aitoc\OrdersExportImport\Model\Profile;
use Magento\Framework\Exception\LocalizedException;

class Export extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::save';
    const URL_HISTORY = 'ordersexportimport/export/index';
    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->getRequest()->getParam('profile_id')) {
            $id = $this->getRequest()->getParam('profile_id');

            $model = $this->_objectManager->create('Aitoc\OrdersExportImport\Model\Profile')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This profile no longer exists.'));

                return $resultRedirect->setPath('sales/order');
            }

            try {
                $export = $this->addExport($model);
                $this->setParams($export);
                $stack = $export->addStack();
                if (!$model->getFlagAuto()) {
                    $this->_objectManager->get('Aitoc\OrdersExportImport\Cron\Export')->setRun($stack->getId())->execute();
                    $this->messageManager->addSuccess(__('Finish export'));
                    return $resultRedirect->setPath('ordersexportimport/export');
                }
                $url = $this->_backendUrl->getUrl(self::URL_HISTORY);
                $this->messageManager->addSuccess(__('You begin to export the profile. You can see the export status
                <a href="' . $url . '">here</a>'));
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while exporting the profile.'));
            }
        }

        return $resultRedirect->setPath('sales/order');
    }

    public function addExport(Profile $profile)
    {
        $params = $this->getRequest()->getParams();
        $config = $profile->getUnsConfig();
        $export = $this->_objectManager->create('Aitoc\OrdersExportImport\Model\Export');
        $export->setProfileId($profile->getId());
        $export->setDt(date('Y-m-d H:i:s'));
        $export->setSerializedConfig(serialize($params));
        if ($config['export_type'] < 3) {
            $export->setIsCron(0);
        } else {
            $export->setIsCron(1);
        }
        $export->setTypeFile(0);
        if ($config['prefix']) {
            $prefix = $config['prefix'];
        } else {
            $prefix = 'order_export';
        }
        $export->setFileName(
            $prefix . '_'  . $profile->getId() . date('YmdHis') . $profile->getTypeFile()
        );

        return $export;
    }

    public function setParams($export)
    {
        $export->setSerializedConfig(serialize($this->getRequest()->getParams()));
        $export->save();
    }
}
