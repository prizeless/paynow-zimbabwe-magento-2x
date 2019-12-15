<?php

namespace Prizeless\PayNow\Controller\Confirm;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Setup\Exception;

class Index extends Action
{
    private $invoice;

    private $transaction;

    private $order;

    private $mailer;

    public function __construct(
        Context $context,
        OrderInterface $order,
        InvoiceService $invoiceService,
        Transaction $transaction,
        Order\Email\Sender\InvoiceSender $mailer
    ) {
        parent::__construct($context);

        $this->order = $order;

        $this->invoice = $invoiceService;

        $this->transaction = $transaction;

        $this->mailer = $mailer;
    }

    public function execute()
    {
        $orderNumber = $_GET['order'];
        $this->updateOrder($orderNumber);
        die();
    }

    private function updateOrder($orderNumber)
    {
        $order = $this->order->loadByIncrementId($orderNumber);
        $this->createInvoice($order);
        $this->updateStatus($order);
    }

    private function updateStatus($order, $ref = '')
    {
        $order->setState(Order::STATE_PROCESSING, true);
        $order->setStatus(Order::STATE_PROCESSING);
        $order->addStatusToHistory($order->getStatus(), 'Payment processed successfully with PayNow reference' . $ref);
        $order->save();
    }

    private function createInvoice($order)
    {
        try {
            if ($order->canInvoice()) {
                $invoice = $this->invoice->prepareInvoice($order);
                $invoice->register();
                $invoice->save();
                $transactionSave = $this->transaction->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $transactionSave->save();
                $this->mailer->send($invoice);
                $order->addStatusHistoryComment(__('PayNow Notified customer about invoice #%1.', $invoice->getId()))
                      ->setIsCustomerNotified(true)
                      ->save();
            }
        } catch (Exception $exception) {
        }
    }
}
