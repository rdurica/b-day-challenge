<?php

declare(strict_types=1);

namespace App\Component\Form\CompleteTask;

/**
 * ICompleteTaskForm interface.
 *
 * @package   App\Component\Form\CompleteTask
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
interface ICompleteTaskForm
{
    /**
     * Create form.
     *
     * @return CompleteTaskForm
     */
    public function create(): CompleteTaskForm;
}