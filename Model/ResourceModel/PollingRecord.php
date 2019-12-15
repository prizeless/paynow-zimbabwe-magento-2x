<?php


namespace Prizeless\PayNow\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PollingRecord extends AbstractDb
{
    public function _construct()
    {
        $this->_init('paynow_transactions','id');
    }
}
