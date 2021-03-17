<?php
/**
 * @category   Mageants AlsoBought
 * @package    Mageants_AlsoBought
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@Mageants.com>
 */
namespace Mageants\AlsoBought\Observer;

class SetBlockPosition implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Mageants\AlsoBought\Helper\Data
     */
    protected $_helper;

    public function __construct(
        \Mageants\AlsoBought\Helper\Data $helper
    ){
    	 $this->_helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$action = $observer->getEvent();
      $fullActionName = $action->getFullActionName();
      if ($fullActionName=='catalog_product_view') {
      	$getPosition = explode(':',$this->_helper->getModuleConfig('alsobought_section/alsobought_product/alsobought_product_position'));
  	    if($getPosition[0] =="replace"){
  	    	$myXml = '<referenceBlock name="'.$getPosition[1].'" remove="true"/>';
  	    }
  	    else{
  	    	$myXml = '<move element="product.also.bought" destination="'.$getPosition[0].'" '.$getPosition[1].'="'.$getPosition[2].'"/>';
  	    }
  	    $layout = $observer->getEvent()->getLayout();
  	    $layout->getUpdate()->addUpdate($myXml);
  	    $layout->generateXml();
      }

      if ($fullActionName=='checkout_cart_index') {
      	$getPosition = explode(':',$this->_helper->getModuleConfig('alsobought_section/alsobought_cart/alsobought_cart_position'));
  	    if($getPosition[0] =="replace"){
  	    	$myXml = '<referenceBlock name="'.$getPosition[1].'" remove="true"/>';
  	    }
  	    else{
  	    	$myXml = '<move element="cart.also.bought" destination="'.$getPosition[0].'" '.$getPosition[1].'="'.$getPosition[2].'"/>';
  	    }
  	    $layout = $observer->getEvent()->getLayout();
  	    $layout->getUpdate()->addUpdate($myXml);
  	    $layout->generateXml();
      }
      return $this;
    }
}