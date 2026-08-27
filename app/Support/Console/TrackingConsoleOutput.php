<?php

declare(strict_types=1);

namespace App\Support\Console;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\Output;

/**
 * Output Symfony qui transmet chaque écriture à un callback (suivi live).
 *
 * @example
 * $out = new TrackingConsoleOutput(fn (string $chunk) => $buffer .= $chunk);
 * Artisan::call('list', [], $out);
 */
final class TrackingConsoleOutput extends Output
{
    /**
     * @param  callable(string): void  $onChunk
     */
    public function __construct(
        private $onChunk,
        int $verbosity = self::VERBOSITY_NORMAL,
    ) {
        parent::__construct($verbosity, false, new OutputFormatter(false));
    }

    protected function doWrite(string $message, bool $newline): void
    {
        ($this->onChunk)($message.($newline ? PHP_EOL : ''));
    }
}
