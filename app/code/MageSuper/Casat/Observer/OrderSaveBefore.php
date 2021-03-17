<?php
namespace MageSuper\Casat\Observer;

class OrderSaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    protected $directory;

    public function __construct(\Magento\Directory\Helper\Data $directory)
    {
        $this->directory = $directory;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getOrder();
        $items = $order->getAllItems();
        $allVirtual = true;
        foreach ($items as $item) {
            if ($item->getProductType() != 'virtual') {
                $allVirtual = false;
                break;
            }
        }
        if ($allVirtual) {
            if ($order->getStatus() == 'complete') {
                $order->setStatus('complete_virtual');
            }
        }

        $this->updateProfitMargin($order);
    }

    public function updateProfitMargin($order)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $items = $order->getAllItems();
        $total_cost = $total_price = 0;
        $cost_parent = [];
        foreach ($items as $item) {
            if (!$item->getParentItem()) {
                continue;
            }
            $cost = $item->getBaseCost();
            if ($cost) {
                $product = $item->getProduct();
                $vendor_currency = $product->getData('vendor_currency');
                $attr = $product->getResource()->getAttribute('vendor_currency');
                $optionText = 'CAD';
                if ($attr->usesSource()) {
                    $optionText = $attr->getSource()->getOptionText($vendor_currency);
                }
                if ($vendor_currency && $optionText !== 'CAD') {
                    $cost = $this->directory->currencyConvert($cost, $optionText, 'CAD');
                }
                $cost = $cost * $item->getQtyOrdered();
                if (!isset($cost_parent[$item->getParentItem()->getQuoteItemId()])) {
                    $cost_parent[$item->getParentItem()->getQuoteItemId()] = 0;
                }
                $cost_parent[$item->getParentItem()->getQuoteItemId()] += $cost;

            }
        }
        foreach ($items as $item) {
            if (!$item->getParentItem()) {
                if (isset($cost_parent[$item->getQuoteItemId()])) {
                    $cost = $cost_parent[$item->getQuoteItemId()];
                } else {
                    $cost = $item->getBaseCost();
                    $product = $item->getProduct();
                    if($product){
                        $vendor_currency = $product->getData('vendor_currency');
                        $attr = $product->getResource()->getAttribute('vendor_currency');
                        $optionText = 'CAD';
                        if ($attr->usesSource()) {
                            $optionText = $attr->getSource()->getOptionText($vendor_currency);
                        }
                        if ($vendor_currency && $optionText !== 'CAD') {
                            $cost = $this->directory->currencyConvert($cost, $optionText, 'CAD');
                        }
                    }
                    $cost = $cost * $item->getQtyOrdered();
                }
                $total_cost += $cost;
                $price = $item->getRowTotal();
                $total_price += $price;
                $profit = $price - $cost;
                $item->setProfit($profit);
                if ($price && $price>0) {
                    $margin = $profit * 100 / $price;
                    $item->setMargin($margin);
                }
            }
        }

        $total_profit = $total_price - $total_cost;
        $order->setProfit($total_profit);
        if ($total_price>0){
            $margin = $total_profit * 100 / $total_price;
            $order->setMargin($margin);
        } else {
            $order->setMargin(0);
        }
    }
}
