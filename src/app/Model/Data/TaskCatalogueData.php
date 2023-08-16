<?php

declare(strict_types=1);

namespace App\Model\Data;

use Nette\Utils\DateTime;

/**
 * TaskCatalogueData.
 *
 * @package   App\Model\Data
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class TaskCatalogueData
{
    /** @var int|null Task id. */
    public ?int $id = null;

    /** @var string|null Task summary. */
    public ?string $summary = null;

    /** @var string|null Task description. */
    public ?string $description = null;

    /** @var DateTime|null Task start date. */
    public ?DateTime $startDate = null;

    /** @var DateTime|null Task due date. */
    public ?DateTime $dueDate = null;

    /** @var bool Are photos required for completion? */
    public bool $requirePhotos = false;

    /** @var bool Is video required for completion? */
    public bool $requireVideo = false;

    /** @var bool Is text required for completion? */
    public bool $requireText = true;

    /** @var bool Is task enabled? */
    public bool $isEnabled = true;
}