<?php

declare(strict_types=1);

namespace App\Model\Data;

/**
 * LoginData.
 *
 * @package   App\Model\Data
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class LoginData
{
    /** @var string|null Form field. */
    public ?string $username = null;

    /** @var string|null Form field. */
    public ?string $password = null;
}
