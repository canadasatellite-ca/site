<?php
namespace MageSuper\Casat\Api;

use MageSuper\Casat\Api\Data\OrderCommentInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;

interface OrderCommentRepositoryInterface 
{
    function save(OrderCommentInterface $page);

    function getById($id);

    function getList(SearchCriteriaInterface $criteria);

    function delete(OrderCommentInterface $page);

    function deleteById($id);
}
