<?php

declare(strict_types=1);

namespace App\Component\Form\Rules;

/**
 * IRulesForm interface.
 *
 * @package   App\Component\Form\Rules
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
interface IRulesForm
{
    /**
     * Create Rules form.
     *
     * @return RulesForm
     */
    public function create(): RulesForm;
}
