<?php

namespace Brsw\Reindexer\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Indexer\Model\IndexerFactory;
use \Magento\Framework\Exception\LocalizedException;

/**
 * Class Index
 */
class Index extends Action
{
    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    private $indexerFactory;

    /**
     * Index constructor.
     * @param IndexerFactory $indexerFactory
     * @param Context $context
     */
    public function __construct(
        IndexerFactory $indexerFactory,
        Context $context
    ) {
        $this->indexerFactory = $indexerFactory;
        parent::__construct($context);
    }

    /**
     * Function execute
     * @return void
     * @return void
     */
    public function execute()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (!is_array($indexerIds)) {
            $this->messageManager->addError(__('Please select indexers.'));
        } else {
            try {
                foreach ($indexerIds as $indexerId) {
                    $this->runIndexer($indexerId);
                }
                $this->messageManager->addSuccess(
                    __('The request for updating the indexes was successful.', count($indexerIds))
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __("We couldn't change indexer(s)' mode because of an error.")
                );
            }
        }
        $this->_redirect('indexer/indexer/list');
    }

    /**
     * Run indexer buy ID
     * @param $indexerId
     * @return void
     */
    private function runIndexer($indexerId)
    {
        /** @var \Magento\Indexer\Model\Indexer $indexer */
        $indexer = $this->indexerFactory->create();
        /** @var \Magento\Indexer\Model\Indexer $idx */
        $idx = $indexer->load($indexerId);
        $idx->reindexAll();
    }
}
