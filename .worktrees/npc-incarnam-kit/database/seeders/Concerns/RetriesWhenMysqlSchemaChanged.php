<?php

declare(strict_types=1);

namespace Database\Seeders\Concerns;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Réessaie une écriture BDD lorsque MySQL signale un DDL concurrent (1412) ou un deadlock (1213)
 * en suite PHPUnit longue.
 */
trait RetriesWhenMysqlSchemaChanged
{
    /**
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    protected function retryOnMysqlSchemaChanged(callable $callback, int $maxAttempts = 8): mixed
    {
        $attempt = 0;
        while (true) {
            try {
                return $callback();
            } catch (QueryException $e) {
                $attempt++;
                if ($attempt >= $maxAttempts || ! $this->isRetriableMysqlConcurrencyError($e)) {
                    throw $e;
                }
                // 1412 invalide la transaction courante : reconnecter avant de réessayer.
                DB::connection()->disconnect();
                DB::connection()->reconnect();
                // Backoff progressif : DDL concurrent (migrate:fresh) ou table absente momentanément.
                usleep(100_000 * $attempt * $attempt);
            }
        }
    }

    private function isRetriableMysqlConcurrencyError(QueryException $e): bool
    {
        $previous = $e->getPrevious();
        if ($previous instanceof \PDOException) {
            $code = (int) $previous->getCode();
            if ($code === 1412 || $code === 1213 || $code === 1146 || $code === 1054) {
                return true;
            }
        }

        $message = $e->getMessage();

        return str_contains($message, '1412')
            || str_contains($message, '1213')
            || str_contains($message, '1146')
            || str_contains($message, '1054')
            || str_contains($message, "doesn't exist")
            || str_contains($message, 'Unknown column')
            || str_contains($message, 'Deadlock');
    }
}
