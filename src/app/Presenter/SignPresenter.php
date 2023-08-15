<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Component\Form\Login\ILoginForm;
use App\Component\Form\Login\LoginForm;
use Nette\DI\Attributes\Inject;
use Rdurica\Core\Presenter\Presenter;
use Rdurica\Core\Presenter\RequireAnonymousUser;
use Rdurica\Core\Presenter\SetMdbTemplateLayout;

/**
 * Login user.
 *
 * @package   App\Presenter
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class SignPresenter extends Presenter
{
    use SetMdbTemplateLayout;
    use RequireAnonymousUser;

    #[Inject]
    public ILoginForm $loginForm;

    /**
     * Login form.
     *
     * @return LoginForm
     */
    protected function createComponentLoginForm(): LoginForm
    {
        return $this->loginForm->create();
    }
}
