<?php
namespace MageSuper\Casat\Observer;
use Magento\Catalog\Model\Product as P;
use Magento\Framework\Event\Observer as Ob;
use Magento\Framework\Model\AbstractModel as M;
# 2021-04-24 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
# "Refactor the `MageSuper_Casat` module": https://github.com/canadasatellite-ca/site/issues/73
class ProductSaveBefore implements \Magento\Framework\Event\ObserverInterface {
    /**
     * @param Ob $ob
     * @return void
     */
    function execute(Ob $ob) {
        $p = $ob['product']; /** @var P $p */
		if ($p->getStoreId() && df_backend_user()) {
			$cDesc = df_product_att_changed($p, 'description'); /** @var bool $cDesc */
			$cName = df_product_att_changed($p, 'name'); /** @var bool $cName */
			if ($cDesc || $cName) {
				$fields = $cDesc && $cName ? 'name and description' : ($cName ? 'name' : 'description');
				$is = $cDesc && $cName ? 'are' : 'is';
				df_message_notice(
					$m = "The product's $fields $is changed in a non-default scope! (Product ID: {$p->getEntityId()})"
				);
				# 2021-03-21 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				# "MageSuper_Casat:
				# «You just catched on trying to change product name and(or) description for not default store»":
				# https://github.com/canadasatellite-ca/site/issues/27
				df_log_l($this, $m);
			}
		}
        if (df_product_is_bundle($p)) {
            $totalCost = 0;
			foreach (df_eta($p->getBundleSelectionsData()) as $selection_data) {
				$id = $selection_data[0]['product_id'];
				$cost = $p->getResource()->getAttributeRawValue($id, 'cost', 0);
				$cost = str_replace(',', '', $cost);
				$vendor_currency = $p->getResource()->getAttributeRawValue($id, 'vendor_currency', 0);
				$attr = $p->getResource()->getAttribute('vendor_currency');
				$optionText = 'CAD';
				if ($attr->usesSource()) {
					$optionText = $attr->getSource()->getOptionText($vendor_currency);
				}
				if ($vendor_currency && $optionText !== 'CAD') {
					# 2021-06-20 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
					# 1) «A non-numeric value encountered in vendor/mage2pro/core/Directory/lib/currency/convert.php on line 73»:
					# https://github.com/canadasatellite-ca/site/issues/174
					# 2) $cost could be an empty string here. It causes the error mentioned above.
					$cost = df_currency_convert((float)$cost, $optionText, 'CAD');
				}
				if ($cost == !NULL) {
					$totalCost += ($cost * $selection_data[0]['selection_qty']);
				}
			}
            $cost = $totalCost;
        }
        else {
            $cost = $p->getCost();
            $cost = str_replace(',', '', $cost);
            $vendor_currency = $p->getData('vendor_currency');
            $attr = $p->getResource()->getAttribute('vendor_currency');
            $optionText = 'CAD';
            if ($attr->usesSource()) {
                $optionText = $attr->getSource()->getOptionText($vendor_currency);
            }
            if ($vendor_currency && $optionText !== 'CAD') {
				# 2021-06-20 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				# 1) «A non-numeric value encountered in vendor/mage2pro/core/Directory/lib/currency/convert.php on line 73»:
				# https://github.com/canadasatellite-ca/site/issues/174
				# 2) $cost could be an empty string here. It causes the error mentioned above.
                $cost = df_currency_convert((float)$cost, $optionText, 'CAD');
            }
        }
        if ($cost > 0) {
            $cost = str_replace(',', '', $cost);
            $price = str_replace(',', '', $p->getPrice());
            $p->setPrice($price);
            $price = str_replace(',', '', $p->getSpecialPrice());
            $p->setSpecialPrice($price);
            $p->setData('force_adminprices',true);
            $price = $p->getFinalPrice();
            $p->setData('force_adminprices',false);
            $profit = $price - $cost;
            $p->setProfit($profit);
            $margin = 0;
            if ($price){
                $margin = $profit * 100 / $price;
            }
            $p->setMargin($margin);
        }
    }
}