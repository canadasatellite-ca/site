<?php
namespace MageSuper\Casat\Observer;

use Magento\Framework\App\RequestInterface;

class FixFirstdataCCNumberName implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var RequestInterface $request */
        $request = $observer->getData('request');
        $payment = $request->getParam('payment');
        if (isset($payment['method']) && $payment['method']=='md_firstdata') {
            $payment['cc_type'] = $payment['cc_type_firstdata'];
            $payment['cc_number'] = $payment['cc_number_firstdata'];
            $payment['expiration'] = $payment['expiration_firstdata'];
            $payment['expiration_yr'] = $payment['expiration_yr_firstdata'];
            $payment['cc_cid'] = $payment['cc_cid_firstdata'];
            $payment['save_card'] = $payment['save_card_firstdata'];
            $request->setParam('payment',$payment);

            $post = new \Zend\Stdlib\Parameters($_POST);
            $post->set('payment',$payment);
            $request->setPost($post);
        }
    }
}
