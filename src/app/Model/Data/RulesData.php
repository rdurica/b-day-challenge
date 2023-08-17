<?php

declare(strict_types=1);

namespace App\Model\Data;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * RulesFormData.
 *
 * @package   App\Model\Data
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class RulesData implements IteratorAggregate
{
    /** @var string|null Form field. */
    public ?string $message = null;

    /** @inheritDoc */
    public function getIterator(): Traversable
    {
        return new ArrayIterator([
            'message' => $this->message,
        ]);
    }
}
