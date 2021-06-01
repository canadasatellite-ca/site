<?php

namespace CanadaSatellite\Theme\Plugin\Helper\Product;

use Magento\Catalog\Helper\Product\Compare as ParentCompare;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\App\RequestInterface;
use CanadaSatellite\Theme\Controller\Router;

class Compare
{

    const CORRECT_SKU_NUMBER_REGEX_EXP = '/^[a-zA-Z0-9\-\.]{1,}$/';

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $postHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var array
     */
    protected $_idVsSku;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var RequestInterface
     */
    protected $_request;

    function __construct(
        UrlInterface $urlBuilder,
        ProductFactory $productFactory,
        PostHelper $postHelper,
        RequestInterface $requestInterface
    )
    {
        $this->_urlBuilder = $urlBuilder;
        $this->_productFactory = $productFactory;
        $this->postHelper = $postHelper;
        $this->_request = $requestInterface;
    }

    function aroundGetListUrl(ParentCompare $subject, callable $proceed)
    {
        $itemIds = [];
        foreach ($subject->getItemCollection() as $item) {
            $itemIds[] = $item->getId();
        }

        $sessionIds = [];
        if (isset($_SESSION['catalog']['is_incognito']) && $_SESSION['catalog']['is_incognito'] == 1) {
            if (isset($_SESSION['catalog']['compare_ids'])) {
                $sessionIds = $_SESSION['catalog']['compare_ids'];
            }
        }
        $itemIds = array_unique(array_merge_recursive($sessionIds, $itemIds));

        $skuArray = $this->getIdVsSkuArray($itemIds);
        $params = [
            '_direct' => Router::COMPARE_URL_SKU . '/' . implode('/', $skuArray),
        ];

        return $this->_urlBuilder->getUrl(null, $params);
    }

    /**
     * @param string $skuNumber
     * @return bool
     */
    static function isSkuNumberFormatCorrect($skuNumber) {
        $isCorrect = false;
        if ($skuNumber && preg_match(static::CORRECT_SKU_NUMBER_REGEX_EXP, $skuNumber) && strtolower($skuNumber) != 'null') {
            $isCorrect = true;
        };
        return $isCorrect;
    }

    /**
     * Get parameters to remove products from compare list
     *
     * @param ParentCompare $subject
     * @param string $result
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    function afterGetPostDataRemove(ParentCompare $subject, $result, $product)
    {
        $productIds = $this->_request->getParam('items');
        $ids = explode(',', $productIds);
        $idVsSkuArray = $this->getIdVsSkuArray($ids);
        unset($idVsSkuArray[$product->getId()]);
        $urlWithoutItem = implode('/', $idVsSkuArray);
        $data = [
            \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => '',
            'product' => $product->getId(),
            'confirmation' => true,
            'confirmationMessage' => __('Are you sure you want to remove this item from your Compare Products list?'),
            'urlWithoutItem' => $urlWithoutItem // Example: [6024/AHKT1301] = [6024/BPKT0801/AHKT1301] - [BPKT0801]
        ];
        return $this->postHelper->getPostData($subject->getRemoveUrl(), $data);
    }

    function getIdVsSkuArray($ids = [], $skus = []) {

        if ($this->_idVsSku === null) {
            $idsArray = [];
            $skusArray = [];
            foreach ($ids as $id) {
                if ($this->isSkuNumberFormatCorrect($id)) {
                    $idsArray[] = $id;
                }
            }
            foreach ($skus as $sku) {
                if ($this->isSkuNumberFormatCorrect($sku)) {
                    $skusArray[] = $sku;
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
                            'in' => $idsArray
                        ],
                        [
                            'attribute' => 'sku',
                            'in' => $skusArray
                        ]
                    ]
                )
                ->load();

            $idVsSkuArray = [];
            foreach ($productCollection as $product) {
                $idVsSkuArray[$product->getId()] = $this->isSkuNumberFormatCorrect($product->getSku()) ? $product->getSku() : $product->getId();
            }
            ksort($idVsSkuArray);
            $this->_idVsSku = $idVsSkuArray;
        }

        return $this->_idVsSku;
    }
}
