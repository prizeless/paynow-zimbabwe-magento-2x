<?php

namespace Prizeless\PayNow\Controller\Request;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Paynow\Core\InitResponse;
use Paynow\Payments\Paynow;
use Prizeless\PayNow\Model\Data;

class Index extends Action
{
    private $scopeConfig;

    private $checkoutSession;

    private $store;

    private $urlBuilder;

    protected $request;

    private $encryptor;

    private $data;

    private $session;

    private $order;

    const CONFIG_ROOT = 'payment/paynow/';

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Session $checkoutSession,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        EncryptorInterface $encryptor,
        Order $order,
        Data $data
    ) {
        parent::__construct($context);

        $this->encryptor       = $encryptor;
        $this->scopeConfig     = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->store           = $storeManager;
        $this->urlBuilder      = $urlBuilder;
        $this->order           = $order;
        $this->data            = $data;
    }

    public function execute()
    {
        try {
            $this->getPayNowData();
        } catch (\Exception $exception) {
            $this->session->setFlash(
                'An error occurred whilst processing your order, may you please try again',
                'error'
            );

            return $this->_redirect('checkout/');
        }
    }

    private function getPayNowData()
    {
        if ( ! $this->checkoutSession->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');

            return;
        }

        $lastQuoteId           = $this->checkoutSession->getLastQuoteId();
        $lastOrderId           = $this->checkoutSession->getLastOrderId();
        $order                 = $this->order->loadByAttribute('entity_id', $lastOrderId);
        $lastRecurringProfiles = $this->checkoutSession->getLastRecurringProfileIds();
        if ( ! $lastQuoteId || ( ! $lastOrderId && empty($lastRecurringProfiles))) {
            $this->_redirect('checkout/cart');

            return;
        }

        $orderNumber    = $order->getIncrementId();
        $integrationKey = $this->getIntegrationKey();
        $integrationId  = $this->getIntegrationId();

        $successUrl = $this->urlBuilder->getUrl() . 'paynow/success?order=' . $orderNumber;
        $resultUrl  = $this->urlBuilder->getUrl() . '/paynow/confirm?order=' . $orderNumber;
        $paynow     = new Paynow($integrationId, $integrationKey, $successUrl, $resultUrl);
        $payment    = $paynow->createPayment($orderNumber, $order->getCustomerEmail());
        $payment->add($this->getOrderDescription($orderNumber), $order->getGrandTotal());
        $response = $paynow->send($payment);

        $this->savePollingInfo($response, $orderNumber);

        $this->checkoutSession->clearStorage();

        return $this->_redirect($response->redirectUrl());
    }

    private function savePollingInfo(InitResponse $response, $orderNumber)
    {
        if ($response->success()) {
            $this->savePollingUrl($orderNumber, $response->pollUrl());
        }
    }

    private function savePollingUrl($orderNumber, $url)
    {
        $this->data->addData([
            "order_number" => $orderNumber,
            "polling_url"  => $url,
            'created_at'   => date('Y-m-d H:i')
        ])->save();
    }

    private function getIntegrationKey()
    {
        return $this->getConfig('integration_key');
    }

    private function getIntegrationId()
    {
        return $this->getConfig('integration_id');
    }

    private function getConfig($name)
    {
        $config = $this->scopeConfig->getValue(self::CONFIG_ROOT . $name);

        return $this->encryptor->decrypt($config);
    }

    private function getOrderDescription($orderNumber)
    {
        return $this->store->getWebsite()->getName() . ' Order ' . $orderNumber;
    }
}
