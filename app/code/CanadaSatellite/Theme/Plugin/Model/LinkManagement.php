<?php
namespace CanadaSatellite\Theme\Plugin\Model;
use Magento\Bundle\Api\Data\LinkInterface as ILink;
use Magento\Bundle\Model\LinkManagement as Sb;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException as NSE;
# 2021-03-25
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class LinkManagement extends Sb {
	/**
	 * 2016-01-01
	 * The empty constructor allows us to skip the parent's one.
	 * Magento (at least at 2016-01-01) is unable to properly inject arguments into a plugin's constructor,
	 * and it leads to the error like: «Missing required argument $amount of Magento\Framework\Pricing\Amount\Base».
	 */
	function __construct() {}

	/**
	 * 2021-03-25
	 * @see \Magento\Bundle\Model\LinkManagement::saveChild()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param $sku
	 * @param ILink $linkedProduct
	 * @return bool
	 * @throws CouldNotSaveException
	 * @throws InputException
	 * @throws NSE
	 */
	function aroundSaveChild(Sb $sb, \Closure $f, $sku, ILink $linkedProduct) {
		$product = df_product_r()->get($sku, true);
		if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
			throw new InputException(
				__('The product with the "%1" SKU isn\'t a bundle product.', [$product->getSku()])
			);
		}
		/** @var \Magento\Catalog\Model\Product $linkProductModel */
		$linkProductModel = df_product_r()->get($linkedProduct->getSku());
		if ($linkProductModel->isComposite()) {
			throw new InputException(__('The bundle product can\'t contain another composite product.'));
		}
		if (!$linkedProduct->getId()) {
			throw new InputException(__('The product link needs an ID field entered. Enter and try again.'));
		}
		# 2021-03-26 A custom code BEGIN
		$linkedProduct->setPrice($linkProductModel->getFinalPrice())->setSelectionPriceValue($linkProductModel->getFinalPrice());
		# 2021-03-26 A custom code END
		/** @var \Magento\Bundle\Model\Selection $selectionModel */
		$selectionModel = $sb->bundleSelection->create();
		$selectionModel->load($linkedProduct->getId());
		if (!$selectionModel->getId()) {
			throw new InputException(__(
				'The product link with the "%1" ID field wasn\'t found. Verify the ID and try again.'
				,[$linkedProduct->getId()]
			));
		}
		$linkField = df_metadata_pool()->getMetadata(ProductInterface::class)->getLinkField();
		$selectionModel = $sb->mapProductLinkToSelectionModel(
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
}