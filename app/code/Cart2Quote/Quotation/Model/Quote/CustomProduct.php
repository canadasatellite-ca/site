<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote;

/**
 * Class CustomProduct
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
class CustomProduct
{
    use \Cart2Quote\Features\Traits\Model\Quote\CustomProduct {
        createNewProduct as private traitCreateNewProduct;
        useExistingProduct as private traitUseExistingProduct;
    }

    /**
     * @const custom product sku
     */
    const SKU = 'custom-product';

    /**
     * CustomProduct constructor.
     *
     * @param \Cart2Quote\Quotation\Helper\Data $dataHelper
     * @param \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data $dataHelper,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Cart2Quote\Quotation\Model\Quote $quote
    ) {
        $this->quote = $quote;
        $this->dataHelper = $dataHelper;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * Create new product function
     *
     * @param array $productParams
     * @return array
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function createNewProduct($productParams)
    {
        return $this->traitCreateNewProduct($productParams);
    }

    /**
     * Use the already existing custom product
     *
     * @param array $productParams
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function useExistingProduct($productParams)
    {
        return $this->traitUseExistingProduct($productParams);
    }
}
