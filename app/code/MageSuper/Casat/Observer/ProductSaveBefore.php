<?php


namespace MageSuper\Casat\Observer;

class ProductSaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    protected $authSession;
    protected $messageManager;
    protected $directory;

    function __construct(\Magento\Backend\Model\Auth\Session $authSession,
                                \Magento\Framework\Message\ManagerInterface $messageManager,
                                \Magento\Directory\Helper\Data $directory)
    {
        $this->authSession = $authSession;
        $this->messageManager = $messageManager;
        $this->directory = $directory;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();
        if ($product->getStoreId() != 0) {
            $user = $this->authSession->getUser();
            $name = $product->getName();
            $description = $product->getDescription();

            if ($name || $description) {
                if($user){
                    $message = $user->getUserName() . ', you just catched on trying to change product name and(or) description for not default store!(ID ' . $product->getEntityId() . ')';
                }
                else{
                    $message = 'You just catched on trying to change product name and(or) description for not default store!(ID ' . $product->getEntityId() . ')';
                }
                $this->messageManager->addNoticeMessage($message);
				# 2021-03-21 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				# "MageSuper_Casat:
				# «You just catched on trying to change product name and(or) description for not default store»":
				# https://github.com/canadasatellite-ca/site/issues/27
                df_log_l($this, $message);
            }

        }

        if ($product->getTypeId() == 'bundle') {
            $totalCost = 0;
            $selections_data = $product->getBundleSelectionsData();
            if($selections_data){
                foreach ($selections_data as $selection_data) {
                    $id = $selection_data[0]['product_id'];
                    $cost = $product->getResource()->getAttributeRawValue($id, 'cost', 0);
                    $cost = str_replace(',', '', $cost);
                    $vendor_currency = $product->getResource()->getAttributeRawValue($id, 'vendor_currency', 0);
                    $attr = $product->getResource()->getAttribute('vendor_currency');
                    $optionText = 'CAD';
                    if ($attr->usesSource()) {
                        $optionText = $attr->getSource()->getOptionText($vendor_currency);
                    }
                    if ($vendor_currency && $optionText !== 'CAD') {
                        $cost = $this->directory->currencyConvert($cost, $optionText, 'CAD');
                    }
                    if ($cost == !NULL) {
                        $totalCost += ($cost * $selection_data[0]['selection_qty']);
                    }
                }
            }
            $cost = $totalCost;
        } else {
            $cost = $product->getCost();
            $cost = str_replace(',', '', $cost);
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
        if ($cost > 0) {
            $cost = str_replace(',', '', $cost);
            $price = str_replace(',', '', $product->getPrice());
            $product->setPrice($price);
            $price = str_replace(',', '', $product->getSpecialPrice());
            $product->setSpecialPrice($price);

            $product->setData('force_adminprices',true);
            $price = $product->getFinalPrice();
            $product->setData('force_adminprices',false);
            $profit = $price - $cost;
            $product->setProfit($profit);
            $margin = 0;
            if ($price){
                $margin = $profit * 100 / $price;
            }
            $product->setMargin($margin);
        }
    }
}
