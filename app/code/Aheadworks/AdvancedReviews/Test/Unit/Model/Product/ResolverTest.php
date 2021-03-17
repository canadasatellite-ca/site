<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Product;

use Aheadworks\AdvancedReviews\Model\Product\Resolver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Url;
use Magento\Catalog\Helper\Output;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Product\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $registryMock;

    /**
     * @var ProductRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepositoryMock;

    /**
     * @var Output|\PHPUnit_Framework_MockObject_MockObject
     */
    private $outputHelperMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->registryMock = $this->createMock(Registry::class);
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->outputHelperMock = $this->createMock(Output::class);

        $this->resolver = $objectManager->getObject(
            Resolver::class,
            [
                'registry' => $this->registryMock,
                'request' => $this->requestMock,
                'productRepository' => $this->productRepositoryMock,
                'outputHelper' => $this->outputHelperMock,
            ]
        );
    }

    /**
     * Test getCurrentProductId method - retrieve product from registry
     */
    public function testGetCurrentProductIdFromRegistry()
    {
        $productId = 1;
        $productMock = $this->createMock(ProductInterface::class);
        $productMock->expects($this->once())
            ->method('getId')
            ->willReturn($productId);

        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);

        $this->requestMock->expects($this->never())
            ->method('getParam');

        $this->productRepositoryMock->expects($this->never())
            ->method('getById');

        $this->assertEquals($productId, $this->resolver->getCurrentProductId());
    }

    /**
     * Test getCurrentProductId method - retrieve product from request with exception
     *
     * @param mixed $registryProduct
     * @dataProvider getCurrentProductIdFromRequestExceptionDataProvider
     */
    public function testGetCurrentProductIdFromRequestException($registryProduct)
    {
        $requestId = 1;

        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($registryProduct);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($requestId);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($requestId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertNull($this->resolver->getCurrentProductId());
    }

    /**
     * @return array
     */
    public function getCurrentProductIdFromRequestExceptionDataProvider()
    {
        return [
            [
                'registryProduct' => null,
            ],
            [
                'registryProduct' => '',
            ],
            [
                'registryProduct' => 2,
            ],
            [
                'registryProduct' => $this->createMock(DataObject::class),
            ],
        ];
    }

    /**
     * Test getCurrentProductId method - retrieve product from request
     *
     * @param mixed $registryProduct
     * @dataProvider getCurrentProductIdFromRequestExceptionDataProvider
     */
    public function testGetCurrentProductIdFromRequest($registryProduct)
    {
        $requestId = 1;

        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($registryProduct);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($requestId);

        $productMock = $this->createMock(ProductInterface::class);
        $productMock->expects($this->once())
            ->method('getId')
            ->willReturn($requestId);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($requestId)
            ->willReturn($productMock);

        $this->assertEquals($requestId, $this->resolver->getCurrentProductId());
    }

    /**
     * Test getPreparedProductNameByObject method with exception
     */
    public function testGetPreparedProductNameByObjectException()
    {
        $result = '';

        $name = "Quest <br> Lumaflex&trade; <strong> Band</strong>";
        $productMock = $this->createMock(ProductInterface::class);
        $productMock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        $this->outputHelperMock->expects($this->once())
            ->method('productAttribute')
            ->with($productMock, $name, 'name')
            ->willThrowException(new LocalizedException(__('Error!')));

        $this->assertEquals($result, $this->resolver->getPreparedProductNameByObject($productMock));
    }

    /**
     * Test getPreparedProductNameByObject method
     */
    public function testGetPreparedProductNameByObject()
    {
        $result = "Quest &lt;br&gt; Lumaflex&trade; &lt;strong&gt; Band&lt;/strong&gt;";

        $name = "Quest <br> Lumaflex&trade; <strong> Band</strong>";
        $productMock = $this->createMock(ProductInterface::class);
        $productMock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        $this->outputHelperMock->expects($this->once())
            ->method('productAttribute')
            ->with($productMock, $name, 'name')
            ->willReturn($result);

        $this->assertEquals($result, $this->resolver->getPreparedProductNameByObject($productMock));
    }

    /**
     * Test getPreparedProductName method - error in the repository
     */
    public function testGetPreparedProductNameErrorInRepository()
    {
        $productId = 11;
        $result = '';

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willThrowException(new NoSuchEntityException());

        $this->outputHelperMock->expects($this->never())
            ->method('productAttribute');

        $this->assertEquals($result, $this->resolver->getPreparedProductName($productId));
    }

    /**
     * Test getPreparedProductName method - error in the output helper
     */
    public function testGetPreparedProductNameErrorInHelper()
    {
        $productId = 11;
        $result = '';

        $name = "Quest <br> Lumaflex&trade; <strong> Band</strong>";
        $productMock = $this->createMock(ProductInterface::class);
        $productMock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $this->outputHelperMock->expects($this->once())
            ->method('productAttribute')
            ->with($productMock, $name, 'name')
            ->willThrowException(new LocalizedException(__('Error!')));

        $this->assertEquals($result, $this->resolver->getPreparedProductName($productId));
    }

    /**
     * Test getPreparedProductName method
     */
    public function testGetPreparedProductName()
    {
        $productId = 11;
        $result = "Quest &lt;br&gt; Lumaflex&trade; &lt;strong&gt; Band&lt;/strong&gt;";

        $name = "Quest <br> Lumaflex&trade; <strong> Band</strong>";
        $productMock = $this->createMock(ProductInterface::class);
        $productMock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $this->outputHelperMock->expects($this->once())
            ->method('productAttribute')
            ->with($productMock, $name, 'name')
            ->willReturn($result);

        $this->assertEquals($result, $this->resolver->getPreparedProductName($productId));
    }

    /**
     * Test getProductReviewUrlByObject method
     */
    public function testGetProductReviewUrlByObject()
    {
        $result = 'www.store.com/index.php/path-to-product/product-name.html#product_aw_reviews_tab';

        $urlModel = $this->createMock(Url::class);

        $product = $this->createMock(Product::class);
        $product->expects($this->once())
            ->method('getUrlModel')
            ->willReturn($urlModel);

        $urlModel->expects($this->once())
            ->method('getUrl')
            ->with(
                $product,
                [
                    '_fragment' => 'product_aw_reviews_tab',
                    '_secure' => true
                ]
            )->willReturn($result);

        $this->assertEquals($result, $this->resolver->getProductReviewUrlByObject($product));
    }

    /**
     * Test getProductReviewUrl method - error in the repository
     */
    public function testGetProductReviewUrlErrorInRepository()
    {
        $productId = 11;
        $result = '';

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertEquals($result, $this->resolver->getProductReviewUrl($productId));
    }

    /**
     * Test getProductReviewUrl method
     */
    public function testGetProductReviewUrlByObjectErrorInRepository()
    {
        $productId = 11;
        $result = 'www.store.com/index.php/path-to-product/product-name.html#product_aw_reviews_tab';

        $urlModel = $this->createMock(Url::class);

        $product = $this->createMock(Product::class);
        $product->expects($this->once())
            ->method('getUrlModel')
            ->willReturn($urlModel);

        $urlModel->expects($this->once())
            ->method('getUrl')
            ->with(
                $product,
                [
                    '_fragment' => 'product_aw_reviews_tab',
                    '_secure' => true
                ]
            )->willReturn($result);

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($product);

        $this->assertEquals($result, $this->resolver->getProductReviewUrl($productId));
    }
}
