<?php

declare(strict_types=1);

namespace App\Model\Data;

use Nette\Database\Table\ActiveRow;

/**
 * HomeTaskData.
 *
 * @package   App\Model\Data
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class HomeTaskData
{
    /** @var ActiveRow|null Task which can be started. */
    public ?ActiveRow $newTask = null;

    /** @var ActiveRow|null Active task (started, assigned). */
    public ?ActiveRow $assignedActiveTask = null;

}