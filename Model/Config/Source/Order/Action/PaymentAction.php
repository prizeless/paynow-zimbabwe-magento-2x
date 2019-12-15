<?php


namespace Prizeless\PayNow\Model\Config\Source\Order\Action;

class PaymentAction
{
    public function toOptionArray()
    {
        return [
            ['value' => 'authorize', 'label' => __('Authorize Only')],
            ['value' => 'authorize_capture', 'label' => __('Authorize and Capture')],
        ];
    }
}
