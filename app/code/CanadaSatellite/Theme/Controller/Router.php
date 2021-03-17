<?php

namespace CanadaSatellite\Theme\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url;
use CanadaSatellite\Theme\Plugin\Helper\Product\Compare;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductFactory;

class Router implements \Magento\Framework\App\RouterInterface
{

    const COMPARE_URL_SKU = 'compare';

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * Router constructor.
     * @param ActionFactory $actionFactory
     */
    public function __construct(
        ActionFactory $actionFactory,
        ProductFactory $productFactory
    ) {
        $this->actionFactory = $actionFactory;
        $this->_productFactory = $productFactory;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $identifierArray = explode ('/', $identifier);
        // comparing/6041-6111-8591
        // http://canada.loc/comparing/BPKT0801/AHKT1301
        if (count($identifierArray) < 2 || $identifierArray[0] !== static::COMPARE_URL_SKU) {
            return null;
        }

        array_shift($identifierArray);
        $productIds = $this->_getProductIdsBySkuNumbers($identifierArray);
        $items = implode(',', $productIds);

        $request
            ->setModuleName('catalog')
            ->setControllerName('product_compare')
            ->setActionName('index')
            ->setParam('items', $items)
            ->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

        return $this->actionFactory->create(Forward::class);
    }

    /**
     * Get Ids by skuNumber or Ids
     *
     * @param array $skuNumbers
     * @return array
     */
    protected function _getProductIdsBySkuNumbers($skuNumbers)
    {
        $list = [];
        foreach ($skuNumbers as $skuNumber) {
            if (Compare::isSkuNumberFormatCorrect($skuNumber)) {
                $list[] = $skuNumber;
            }
        }

        $productCollection = $this->_productFactory->create();
        $productCollection
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('sku')
            ->addAttributeToFilter(
                [
                    [
                        'attribute' => 'entity_id',
                        'in' => $list
                    ],
                    [
                        'attribute' => 'sku',
                        'in' => $list
                    ]
                ]
            )
            ->load();

        // create array product Id=>SkuNumber. Example: ['6041' => 'BPKT0801', '6111' => 'AHKT1301']
        $idSkuArray = [];
        foreach ($productCollection as $product) {
            $idSkuArray[$product->getId()] = $product->getSku();
        }

        $idsArray = [];
        foreach ($list as $value) {
            if (in_array($value, $idSkuArray)) {
                $idsArray[] = array_search($value, $idSkuArray);
            } else {
                if (key_exists($value, $idSkuArray)) {
                    $idsArray[] = $value;
                }
            }
        }

        return $idsArray;
    }
}
