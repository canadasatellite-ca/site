<?php
namespace MageSuper\Casat\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductAllowOrderOutOfStock implements ObserverInterface
{
    protected $state;
    public function __construct(\Magento\Framework\App\State $state)
    {
        $this->state = $state;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getData('collection');
        if(get_class($collection)=='Magento\CatalogInventory\Model\ResourceModel\Stock\Item\Collection'){
            if($this->state->getAreaCode()=='adminhtml'){
                return;
            }
            $items = $collection->getItems();
            foreach($items as $item){
                $item->setData('is_in_stock',true);
            }
        }
    }
}
