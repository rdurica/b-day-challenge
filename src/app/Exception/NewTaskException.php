<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

/**
 * Occurs when new task is not available.
 *
 * @package   App\Exception
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class NewTaskException extends Exception
{
    /**
     * Konstruktor.
     */
    public function __construct()
    {
        $message = 'Zadny novy ukol nenalezen.';
        parent::__construct($message);
    }
}