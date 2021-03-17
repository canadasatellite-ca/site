<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote;
/**
 * Trait CustomProduct
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
trait CustomProduct
{
    /**
     * Create new product function
     *
     * @param array $productParams
     * @return array
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function createNewProduct($productParams)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$product = $this->productFactory->create();
        if (!$product->getIdBySku($productParams['sku'])) {
            $product->setData($productParams)
                ->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
                ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE)
                ->setAttributeSetId($product->getDefaultAttributeSetId())
                ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                ->setStockData(
                    [
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 0
                    ]
                );
            $product = $this->productRepository->save($product);
            $product->save();
            $customProduct['product_id'] = $product->getEntityId();
            $customProduct['qty'] = $productParams['qty'];
            return $customProduct;
        } else {
            $error['errorMsg'] = 'Product SKU already exists please enter a new SKU!';
            return $error;
        }
		}
	}
    /**
     * Use the already existing custom product
     *
     * @param array $productParams
     * @return \Magento\Framework\Controller\ResultInterface
     */
    private function useExistingProduct($productParams)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$product = $this->productFactory->create();
        $existingProduct = $product->getIdBySku(self::SKU);
        if ($existingProduct) {
            $product->load($existingProduct);
            foreach ($product->getOptions() as $option) {
                $options['product_id'] = $product->getId();
                $options['option_id'] = $option->getId();
                $options['option_value'] = $productParams[$option->getTitle()];
                $customProduct['productOptions'][] = $options;
            }
            $customProduct['product_id'] = $product->getId();
            $customProduct['qty'] = $productParams['qty'];
            return $customProduct;
        } else {
            $error['errorMsg'] = 'No Custom Product available, please see
                <a href="https://cart2quote.zendesk.com/hc/en-us/articles/360022520671" target="_blank">here!</a>';
            return $error;
        }
		}
	}
}
