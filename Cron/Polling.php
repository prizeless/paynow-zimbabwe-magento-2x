<?php

namespace Prizeless\PayNow\Cron;

use Psr\Log\LoggerInterface;

class Polling
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute()
    {
        $this->logger->info('PayNow Cron Works');
    }
}
