<?php

namespace Prizeless\PayNow\Controller\Success;

use Magento\Framework\App\Action\Action;

class Index extends Action
{
    public function execute()
    {
        $orderNumber = $_GET['order'];
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
    <title>Payment Confirmation</title>
    <style type=\"text/css\">
        body {
            background-color:#fff;
            color: #0f5293;
            text-align: center;
        }
    </style>
</head>
<body>
    <p>Your payment for $orderNumber was successful</p>
</body>
</html>";
    }
}
