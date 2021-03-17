<?php


namespace Magics\Gross\Cron;

class Grosscron
{

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
                                \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
                                \Magento\Catalog\Model\Product\Visibility $productVisibility)
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        /*
        ini_set ( 'max_execution_time', 2000000);
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
            foreach ($collection as $product) {
                //if($product->getId() == 7807){
                if($product->getPrice() > 0 && $product->getCost() > 0){
                    if($product->getSpecialPrice() > 0){
                        $grossProfit  = $product->getSpecialPrice() - $product->getCost();
                    }else{
                       	$grossProfit  = $product->getPrice() - $product->getCost();
                    }
                    //echo $product->getId();
                    $grossProfitPersentage = round($grossProfit / $product->getPrice(),3);
                    $product->setData("a_gross_profit",$grossProfit);
                    $product->setData("a_gross_profit_percentage",$grossProfitPersentage);
                    if(!$product->save()){
                        continue;
                    }
                }
            }
        */

    }
}
