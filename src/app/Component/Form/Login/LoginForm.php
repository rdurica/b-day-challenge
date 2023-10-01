<?php

declare(strict_types=1);

namespace App\Component\Form\Login;

use App\Model\Data\LoginData;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Security\AuthenticationException as NetteAuthenticationException;
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
     * @param Translator            $translator
     */
    public function __construct(
        private readonly AuthenticationService $authenticationService,
        private readonly User $user,
        private readonly Translator $translator
    ) {
    }

    /**
     * Login form.
     *
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $labelUsername = $this->translator->translate('labels.username');
        $labelPassword = $this->translator->translate('labels.password');
        $labelButton = $this->translator->translate('labels.login');

        $form = new Form();
        $form->setMappedType(LoginData::class);
        $form->addText('username', $labelUsername)
            ->setRequired();
        $form->addPassword('password', $labelPassword)
            ->setRequired();
        $form->addSubmit('login', $labelButton);

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
        } catch (AuthenticationException|NetteAuthenticationException $e) {
            $this->getPresenter()->flashMessage($e->getMessage(), FlashType::ERROR);
            $this->redirect('this');
        }
    }
}