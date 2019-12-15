<?php

namespace Prizeless\PayNow\Model;

use Magento\Framework\Model\AbstractModel;

class Data extends AbstractModel
{
    public function _construct(){
        $this->_init("Prizeless\PayNow\Model\ResourceModel\PollingRecord");
    }
}
