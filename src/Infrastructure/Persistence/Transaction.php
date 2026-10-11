<?php

declare(strict_types=1);
namespace App\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;

final class Transaction
{
    public static function run(Connection $db, \Closure $operation): mixed
    {
        try {
            return $db->transactional($operation);
        } catch (\Throwable $error) {
            // A failed SQLite deferred constraint at COMMIT leaves the native
            // transaction open even when DBAL has decremented its nesting level.
            try {
                if ($db->isTransactionActive()) {
                    $db->rollBack();
                }
                $native = $db->getNativeConnection();
                if ($native instanceof \PDO && $native->inTransaction()) {
                    $native->rollBack();
                }
            } catch (\Throwable) {
                $db->close();
            }
            throw $error;
        }
    }
}
