<?php

namespace Interactivated\Quotecheckout\Model\Sales\Order\Creditmemo\Total;

class Giftwrap extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * Collect giftwrap amount
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();

        if($order->getGiftwrapAmountInvoiced() > 0) {
            $giftwrapAmountLeft = $order->getGiftwrapAmountInvoiced() - $order->getGiftwrapAmountRefunded();
            $baseGiftwrapAmountLeft = $order->getBaseGiftwrapAmountInvoiced() - $order->getBaseGiftwrapAmountRefunded();

            if ($baseGiftwrapAmountLeft > 0) {
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $giftwrapAmountLeft);
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseGiftwrapAmountLeft);
                $creditmemo->setGiftwrapAmount($giftwrapAmountLeft);
                $creditmemo->setBaseGiftwrapAmount($baseGiftwrapAmountLeft);
            }

        } else {
            $giftwrapAmount = $order->getGiftwrapAmountInvoiced();
            $baseGiftwrapAmount = $order->getBaseGiftwrapAmountInvoiced();

            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $giftwrapAmount);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseGiftwrapAmount);
            $creditmemo->setGiftwrapAmount($giftwrapAmount);
            $creditmemo->setBaseGiftwrapAmount($baseGiftwrapAmount);

        }

        return $this;
    }
}
