<?php
namespace MageSuper\Casat\Api;

use MageSuper\Casat\Api\Data\OrderCommentInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;

interface OrderCommentRepositoryInterface 
{
    public function save(OrderCommentInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(OrderCommentInterface $page);

    public function deleteById($id);
}
