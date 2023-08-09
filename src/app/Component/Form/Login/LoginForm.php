<?php

declare(strict_types=1);

namespace App\Component\Form\Login;

use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\Utils\ArrayHash;
use Rdurica\Core\Component\Component;
use Rdurica\Core\Component\ComponentRenderer;
use Rdurica\Core\Model\Service\UserService;

/**
 * LoginForm.
 *
 * @package   App\Component\Form\Login
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class LoginForm extends Component
{
    use ComponentRenderer;

    public function __construct(private readonly UserService $userService, private readonly User $user)
    {
    }

    public function createComponentForm(): Form
    {
        $form = new Form();
        $form->addText('username', 'Uživatelské jméno')
            ->setRequired();
        $form->addPassword('password', 'Heslo')
            ->setRequired();
        $form->addSubmit('login', 'Přihlásit');

        $form->onSuccess[] = [$this, 'formOnSuccess'];

        return $form;
    }

    /**
     * Process form.
     *
     * @param Form      $form
     * @param ArrayHash $values
     * @return void
     * @throws AbortException
     */
    public function formOnSuccess(Form $form, ArrayHash $values): void
    {
        try {
            $identity = $this->userService->authenticate($values->username, $values->password);
            $this->user->login($identity);
            $this->getPresenter()->redirect('Home:');
        } catch (AuthenticationException $e) {
            $this->getPresenter()->flashMessage($e->getMessage(), 'danger');
            $this->redirect('this');
        }
    }
}