<?php

declare(strict_types=1);

namespace App\Model\Manager;

use Rdurica\Core\Model\Manager\Manager;

final class TaskCatalogueManager extends Manager
{
    /** @var string Table name. */
    private const TABLE = 'task_catalogue';

    /** @inheritDoc */
    protected function getTableName(): string
    {
        return self::TABLE;
    }
}