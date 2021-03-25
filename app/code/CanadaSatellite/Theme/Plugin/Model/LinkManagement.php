<?php
namespace CanadaSatellite\Theme\Plugin\Model;
use Magento\Bundle\Api\Data\LinkInterface as ILink;
use Magento\Bundle\Model\LinkManagement as ParentLinkManagement;
use Magento\Bundle\Model\SelectionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
# 2021-03-25
final class LinkManagement {
	/**
	 * 2021-03-25
	 * @param ParentLinkManagement $subject
	 * @param callable $proceed
	 * @param $sku
	 * @param ILink $linkedProduct
	 * @return bool
	 * @throws CouldNotSaveException
	 * @throws InputException
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	function aroundSaveChild(
		ParentLinkManagement $subject, callable $proceed, $sku, ILink $linkedProduct
	) {
		$product = $this->productRepository->get($sku, true);
		if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
			throw new InputException(
				__('Product with specified sku: "%1" is not a bundle product', [$product->getSku()])
			);
		}

		/** @var \Magento\Catalog\Model\Product $linkProductModel */
		$linkProductModel = $this->productRepository->get($linkedProduct->getSku());
		if ($linkProductModel->isComposite()) {
			throw new InputException(__('Bundle product could not contain another composite product'));
		}

		if (!$linkedProduct->getId()) {
			throw new InputException(__('Id field of product link is required'));
		}

		$linkedProduct->setPrice($linkProductModel->getFinalPrice())->setSelectionPriceValue($linkProductModel->getFinalPrice());

		/** @var \Magento\Bundle\Model\Selection $selectionModel */
		$selectionModel = $this->bundleSelection->create();
		$selectionModel->load($linkedProduct->getId());
		if (!$selectionModel->getId()) {
			throw new InputException(__('Can not find product link with id "%1"', [$linkedProduct->getId()]));
		}
		$linkField = $this->getMetadataPool()->getMetadata(ProductInterface::class)->getLinkField();
		$selectionModel = $this->mapProductLinkToSelectionModel(
			$selectionModel,
			$linkedProduct,
			$linkProductModel->getId(),
			$product->getData($linkField)
		);

		try {
			$selectionModel->save();
		} catch (\Exception $e) {
			throw new CouldNotSaveException(__('Could not save child: "%1"', $e->getMessage()), $e);
		}

		return true;
	}

	/**
	 * Get MetadataPool instance
	 * @return MetadataPool
	 */
	private function getMetadataPool()
	{
		if (!$this->metadataPool) {
			$this->metadataPool = ObjectManager::getInstance()->get(MetadataPool::class);
		}
		return $this->metadataPool;
	}

	/**
	 * @param \Magento\Bundle\Model\Selection $selectionModel
	 * @param \Magento\Bundle\Api\Data\LinkInterface $productLink
	 * @param string $linkedProductId
	 * @param string $parentProductId
	 * @return \Magento\Bundle\Model\Selection
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	protected function mapProductLinkToSelectionModel(
		\Magento\Bundle\Model\Selection $selectionModel,
		\Magento\Bundle\Api\Data\LinkInterface $productLink,
		$linkedProductId,
		$parentProductId
	) {
		$selectionModel->setProductId($linkedProductId);
		$selectionModel->setParentProductId($parentProductId);
		if ($productLink->getSelectionId() !== null) {
			$selectionModel->setSelectionId($productLink->getSelectionId());
		}
		if ($productLink->getOptionId() !== null) {
			$selectionModel->setOptionId($productLink->getOptionId());
		}
		if ($productLink->getPosition() !== null) {
			$selectionModel->setPosition($productLink->getPosition());
		}
		if ($productLink->getQty() !== null) {
			$selectionModel->setSelectionQty($productLink->getQty());
		}
		if ($productLink->getPriceType() !== null) {
			$selectionModel->setSelectionPriceType($productLink->getPriceType());
		}
		if ($productLink->getPrice() !== null) {
			$selectionModel->setSelectionPriceValue($productLink->getPrice());
		}
		if ($productLink->getCanChangeQuantity() !== null) {
			$selectionModel->setSelectionCanChangeQty($productLink->getCanChangeQuantity());
		}
		if ($productLink->getIsDefault() !== null) {
			$selectionModel->setIsDefault($productLink->getIsDefault());
		}

		return $selectionModel;
	}

	/**
	 * @var \Magento\Catalog\Api\ProductRepositoryInterface
	 */
	protected $productRepository;

	/**
	 * @var SelectionFactory
	 */
	protected $bundleSelection;

	/**
	 * @var MetadataPool
	 */
	private $metadataPool;

	function __construct(
		ProductRepositoryInterface $productRepository,
		SelectionFactory $bundleSelection
	){
		$this->productRepository = $productRepository;
		$this->bundleSelection = $bundleSelection;
	}
}