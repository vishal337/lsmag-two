<?php

namespace Ls\Replication\Model\ResourceModel\ItemUOM;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    public function _construct()
    {
        $this->_init( 'Ls\Replication\Model\ItemUOM', 'Ls\Replication\Model\ResourceModel\ItemUOM' );
    }


}
