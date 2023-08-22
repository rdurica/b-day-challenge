<?php

declare(strict_types=1);

namespace App\Component\Form\Login;

use App\Model\Data\LoginData;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Rdurica\Core\Component\Component;
use Rdurica\Core\Component\ComponentRenderer;
use Rdurica\Core\Constant\FlashType;
use Rdurica\Core\Exception\Authentication\AuthenticationException;
use Rdurica\Core\Model\Service\AuthenticationService;

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

    /**
     * Constructor.
     *
     * @param AuthenticationService $authenticationService
     * @param User                  $user
     */
    public function __construct(
        private readonly AuthenticationService $authenticationService,
        private readonly User $user
    ) {
    }

    /**
     * Login form.
     *
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = new Form();
        $form->setMappedType(LoginData::class);
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
     * @param LoginData $data
     * @return void
     * @throws AbortException
     */
    public function formOnSuccess(Form $form, LoginData $data): void
    {
        try {
            $identity = $this->authenticationService->authenticate($data->username, $data->password);
            $this->user->login($identity);
            $this->getPresenter()->redirect('Home:');
        } catch (AuthenticationException $e) {
            $this->getPresenter()->flashMessage($e->getMessage(), FlashType::ERROR);
            $this->redirect('this');
        }
    }
}