<?php

declare(strict_types=1);

namespace App\Component\Form\Login;

/**
 * LoginForm interface.
 *
 * @package   App\Component\Form\Login
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
interface ILoginForm
{
    /**
     * Create login form.
     *
     * @return LoginForm
     */
    public function create(): LoginForm;
}