<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_REASSIGN_ORDER
 * @copyright  Copyright (c) 2016 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */
namespace Itoris\ReassignOrder\Block\Adminhtml\Order\Customer;
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    public function __construct( \Magento\Backend\Block\Template\Context $context,
                                 \Magento\Backend\Helper\Data $backendHelper,
                                 array $data = [])
    {
        parent::__construct($context,$backendHelper,$data);
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
    }
    protected $_objectManager;
    public function getObjectManager(){
        if($this->_objectManager)
            return $this->_objectManager;
        return $this->_objectManager= \Magento\Framework\App\ObjectManager::getInstance();
    }
    public function getCoreRegistry(){
        return $this->getObjectManager()->get('Magento\Framework\Registry');
    }
    protected function _prepareCollection()
    {

        $order =$this->getCoreRegistry()->registry('current_order');
        $collection = $this->getObjectManager()->create('Magento\Customer\Model\ResourceModel\Customer\Collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('entity_id', array(
            'header'    => __('ID'),
            'index'     => 'entity_id',
            'type'  => 'number',
        ));

        $this->addColumn('name', array(
            'header'    => __('Name'),
            'index'     => 'name',
            'align'     => 'center',
            'width'     => '200'
        ));

        $this->addColumn('email', array(
            'header'    => __('Email'),
            'width'     => '150',
            'align'     => 'center',
            'index'     => 'email'
        ));

        $groups = $this->getObjectManager()->create('Magento\Customer\Model\ResourceModel\Group\Collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header'    =>  __('Group'),
            'width'     =>  '100',
            'align'     => 'center',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => __('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => $this->getObjectManager()->create('Magento\Store\Model\System\Store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }

        $this->addColumn('action',
            array(
                'header'    =>  __('Action'),
                'width'     => '100',
                'type'      => 'text',
                'align'     => 'center',
                'renderer'    => 'Itoris\ReassignOrder\Block\Adminhtml\Order\Customer\Render\Action'

            ));
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/loadCustomerGrid', array('_current'=> true));
    }

    protected function _prepareMassaction(){
        return $this;
    }

    public function getRowUrl($row) {
        return null;
    }
}
