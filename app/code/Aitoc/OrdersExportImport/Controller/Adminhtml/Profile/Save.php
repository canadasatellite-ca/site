<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action;
use Aitoc\OrdersExportImport\Model\Profile;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    use \Aitoc\OrdersExportImport\Traits\Additional;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::save';

    const BY_CRON = 3;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

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
        $data           = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('profile_id');

            if (empty($data['profile_id'])) {
                $data['profile_id'] = null;
            }

            $model = $this->_objectManager->create('Aitoc\OrdersExportImport\Model\Profile')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This profile no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }
            $model->setData($this->scopeData($data));

            try {
                $model->save();
                if ($model->getFlagAuto() == self::BY_CRON) {
                    $this->addExport($model, []);
                } else {
                    $this->deleteRecords($model->getId());
                }
                $this->messageManager->addSuccess(__('You saved the profile.'));
                $this->dataPersistor->clear('orderexportimport_profile');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['profile_id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the profile.'));
            }

            $this->dataPersistor->set('orderexportimport_profile', $data);

            return $resultRedirect->setPath('*/*/edit', ['profile_id' => $this->getRequest()->getParam('profile_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function scopeData($data)
    {
        $newData               = [];
        $newData['name']       = $data['name'];
        $newData['profile_id'] = $data['profile_id'];
        $pattern               = '/\/$/mi';
        preg_match($pattern, $data['path_local'], $matches);
        if (!count($matches)) {
            $data['path_local'] .= "/";
        }
        $newData['config']    = serialize($data);
        $newData['date']      = date('Y-m-d H:i:s');
        $newData['flag_auto'] = $data['export_type'];

        return $newData;

    }

    /**
     * @param \Aitoc\OrdersExportImport\Api\Data\ProfileInterface $profile
     * @param $params
     */
    public function addExport(\Aitoc\OrdersExportImport\Api\Data\ProfileInterface $profile, $params)
    {
        $config = $profile->getUnsConfig();
        if ($config['export_cron'] > 0) {
            $export = \Magento\Framework\App\ObjectManager::getInstance()->create('Aitoc\OrdersExportImport\Model\Export');
            $export->setProfileId($profile->getId());
            $export->setDt(date('Y-m-d H:i:s'));
            $export->setSerializedConfig(serialize($params));
            $export->setIsCron(1);
            $export->setTypeFile(0);
            if ($config['prefix']) {
                $prefix = $config['prefix'];
            } else {
                $prefix = 'order_export';
            }
            $export->setFileName(
                $prefix . '_' . $profile->getId() . date('YmdHis') . $profile->getTypeFile()
            );
            $export->setOrdersCount(1);
            if (!$export->getCollection()
                ->addFieldToFilter('profile_id', $profile->getId())
                ->addFieldToFilter('is_cron', 1)
                ->getSize()
            ) {
                $export->save();
                $newDate = $this->addDate($config['export_cron']);
                $export->addStack($newDate);
            }
        } else {
            $this->deleteRecords($profile->getId());
        }
    }

    public function deleteRecords($id)
    {
        $collection = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Aitoc\OrdersExportImport\Model\Export')
            ->getCollection()
            ->addFieldToFilter('profile_id', $id)
            ->addFieldToFilter('is_cron', 1);
        if ($collection->getSize()) {
            foreach ($collection as $item) {
                $item->delete();
            }
        }
    }
}
