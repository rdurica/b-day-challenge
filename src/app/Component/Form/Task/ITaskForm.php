<?php

declare(strict_types=1);

namespace App\Component\Form\Task;

/**
 * TaskForm interface.
 *
 * @package   App\Component\Form\Task
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
interface ITaskForm
{
    /**
     * Create form.
     *
     * @return TaskForm
     */
    public function create(): TaskForm;
}