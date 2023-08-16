<?php

declare(strict_types=1);

namespace App\Model\Constant;

/**
 * TaskStatuses.
 *
 * @package   App\Model\Constant
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class TaskStatus
{
    /** @var int Active task. */
    public const ACTIVE = 1;

    /** @var int Task completed and waiting for approval. */
    public const CONFIRMED = 2;

    /** @var int Task approved by administrator. */
    public const DONE = 3;

    /** @var int Task not finished in time. */
    public const EXPIRED = 4;

    /** @var int Task rejected by administrator. */
    public const REJECTED = 5;
}
