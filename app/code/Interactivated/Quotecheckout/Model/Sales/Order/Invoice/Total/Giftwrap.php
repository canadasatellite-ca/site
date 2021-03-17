<?php

namespace Interactivated\Quotecheckout\Model\Sales\Order\Invoice\Total;

class Giftwrap extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * Collect giftwrap amount
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $order = $invoice->getOrder();

        $giftwrapAmountLeft = $order->getGiftwrapAmount() - $order->getGiftwrapAmountInvoiced();
        $baseGiftwrapAmountLeft = $order->getBaseGiftwrapAmount() - $order->getBaseGiftwrapAmountInvoiced();

        if (abs($baseGiftwrapAmountLeft) < $invoice->getBaseGrandTotal()) {
            $invoice->setGrandTotal($invoice->getGrandTotal() + $giftwrapAmountLeft);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseGiftwrapAmountLeft);
        } else {
            $giftwrapAmountLeft = $invoice->getGrandTotal() * -1;
            $baseGiftwrapAmountLeft = $invoice->getBaseGrandTotal() * -1;

            $invoice->setGrandTotal(0);
            $invoice->setBaseGrandTotal(0);
        }

        $invoice->setGiftwrapAmount($giftwrapAmountLeft);
        $invoice->setBaseGiftwrapAmount($baseGiftwrapAmountLeft);

        return $this;
    }
}
