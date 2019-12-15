<?php

namespace Prizeless\PayNow\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init("Prizeless\PayNow\Model\Data", "Prizeless\PayNow\Model\ResourceModel\PollingRecord");
    }
}
