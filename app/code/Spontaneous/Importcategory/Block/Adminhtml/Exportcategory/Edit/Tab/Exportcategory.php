<?php
/**
 * Spontaneous_Importcategory extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 * @category  Spontaneous
 * @package   Spontaneous_Importcategory
 * @copyright Copyright (c) 2016
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Spontaneous\Importcategory\Block\Adminhtml\Exportcategory\Edit\Tab;

class Exportcategory extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storemanager = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
        /*$url =  $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $url .= 'spontaneous_categoryimport/spontaneous_categoryimport.zip';*/
        $url = $this->getViewFileUrl('Spontaneous_Importcategory/spontaneous_categoryimport.zip');
        $fieldsetexport = $form->addFieldset(
            'export_fieldset',
            [
                'legend' => __('Export Categories'),
                'class'  => 'fieldset-wide'
            ]
        );
        $_stores = [];
        $stores = $storemanager->getStores();
        foreach ($stores as $key => $store) {
            $_stores[$store->getId()] = $store->getName();
        }
        $fieldsetexport->addField(
            'store_id',
            'select',
            [
                'name'  => 'store_id',
                'label' => __('Export From Store'),
                'text' => __('Export From Store'),
                'values'  => $_stores
            ]
        );
        
        $_field = $fieldsetexport->addField(
            'export_all',
            'submit',
            [
                'name'  => 'export_all',
                'text' => __('Export All Categories'),
                'class' => 'action-default scalable save primary',
                'value'     => __('Export All Categories'),
                'style' => 'width:175px'
            ]
        );
              
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Sample Files'),
                'class'  => 'fieldset-wide'
            ]
        );
        $fieldset->addField(
            'export_sample',
            'button',
            [
                'name'  => 'export_sample',
                'label' => __('Sample Files'),
                'text' => __('Sample Files'),
                'value'     => __('Sample Files'),
                'onclick'   => "javascript:window.location = '$url' ",
            ]
        );
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Export Categories');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
