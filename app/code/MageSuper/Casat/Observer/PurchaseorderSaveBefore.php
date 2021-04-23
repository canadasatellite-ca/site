<?php
namespace MageSuper\Casat\Observer;

class PurchaseorderSaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    protected $supplierRepository;

    function __construct(\Magestore\SupplierSuccess\Model\Repository\SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    function execute(\Magento\Framework\Event\Observer $observer)
    {
        $purchaseorder = $observer->getData('purchaseorder');
        $suplierId = $purchaseorder->getData('supplier_id');
        /** @var \Magestore\SupplierSuccess\Model\Supplier $suplier */
        $suplier = $this->supplierRepository->getById($suplierId);
        if ($suplier) {
            $updates = ['shipping_address','shipping_method','shipping_cost','payment_term','placed_via'];
            foreach($updates as $update){
                if(!$purchaseorder->getData($update)){
                    $purchaseorder->setData($update,$suplier->getData($update));
                }
            }
        }
    }
}
