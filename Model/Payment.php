<?php

namespace Prizeless\PayNow\Model;

use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;

class Payment extends AbstractMethod
{
    const CODE = 'paynow';

    protected $_code = self::CODE;

    protected $_countryFactory;

    protected $_isOffline = true;

    protected $cart = null;

    protected $_canOrder = true;

    protected $_isInitializeNeeded = true;

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $cart = null)
    {
        return parent::isAvailable($cart);
    }

    public function authorize(InfoInterface $payment, $amount)
    {
        $payment->setIsTransactionClosed(false);
        $payment->setIsTransactionPending(true);
    }

    public function getConfigPaymentAction()
    {
        return ($this->getConfigData('order_status') == 'pending_payment') ? null : parent::getConfigPaymentAction();
    }
}
