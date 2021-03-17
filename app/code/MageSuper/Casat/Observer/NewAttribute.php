<?php
namespace MageSuper\Casat\Observer;
use Magento\Framework\App\RequestInterface;
class NewAttribute implements \Magento\Framework\Event\ObserverInterface
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
        $attributeCode = $request->getParam('attribute_code');
        if(!$attributeCode){
            $attributeLabel = $request->getParam('frontend_label')[0];
            $attributeCode = $this->generateCode($attributeLabel);
        }
        $request->setParam('attribute_code',$attributeCode);
    }

    protected function generateCode($label)
    {
        $label = strtolower($label);
        $code = substr(
            preg_replace(
                '/[^a-z_0-9]/',
                '_',
                $label
            ),
            0,
            30
        );
        $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8));
        }
        return $code;
    }
}
