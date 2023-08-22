<?php

declare(strict_types=1);

namespace App\Model\Manager;

use Rdurica\Core\Model\Manager\Manager;

/**
 * TaskAlternativeManager.
 *
 * @package   App\Model\Manager
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class TaskAlternativeManager extends Manager
{
    /** @var string Table name. */
    private const TABLE = 'task_alternative';

    /** @inheritDoc */
    protected function getTableName(): string
    {
        return self::TABLE;
    }
}