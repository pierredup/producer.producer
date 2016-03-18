<?php
/**
 *
 * This file is part of Producer for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Producer\Command;

use Producer\Api\ApiInterface;
use Psr\Log\LoggerInterface;
use Producer\Repo\RepoInterface;

/**
 *
 * @package producer/producer
 *
 */
class Issues extends AbstractCommand
{
    public function __invoke(array $argv)
    {
        $issues = $this->api->issues();
        if (empty($issues)) {
            return;
        }

        $this->logger->info($this->api->getRepoName());
        $this->logger->info('');
        foreach ($issues as $issue) {
            $this->logger->info("    {$issue->number}. {$issue->title}");
            $this->logger->info("        {$issue->url}");
            $this->logger->info('');
        }
    }
}
