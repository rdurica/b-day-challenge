<?php

declare(strict_types=1);

namespace App\Component\Form\Rules;

use App\Model\Data\RulesData;
use App\Model\Manager\RulesManager;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Rdurica\Core\Component\Component;
use Rdurica\Core\Component\ComponentRenderer;
use Rdurica\Core\Constant\FlashType;

/**
 * RulesForm.
 *
 * @package   App\Component\Form\Rules
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class RulesForm extends Component
{
    use ComponentRenderer;

    /**
     * Constructor.
     *
     * @param RulesManager $rulesManager
     * @param Translator   $translator
     */
    public function __construct(private readonly RulesManager $rulesManager, private readonly Translator $translator)
    {
    }

    /**
     * Rules form.
     *
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $labelButton = $this->translator->translate('labels.save');

        $form = new Form();
        $form->setMappedType(RulesData::class);
        $form->addTextArea('message')
            ->setDefaultValue($this->rulesManager->findMessage()->message)
            ->setHtmlAttribute('style', 'height: 70vh;')
            ->setRequired();
        $form->addSubmit('save', $labelButton);

        $form->onSuccess[] = [$this, 'formOnSuccess'];

        return $form;
    }

    /**
     * Process form.
     *
     * @throws AbortException
     */
    public function formOnSuccess(Form $form, RulesData $data): void
    {
        try {
            $message = $this->translator->translate('messages.success.rulesUpdated');
            $this->rulesManager->update($data);
            $this->getPresenter()->flashMessage($message, FlashType::SUCCESS);
        } catch (Exception) {
            $message = $this->translator->translate('messages.error.generalError');
            $this->getPresenter()->flashMessage($message, FlashType::ERROR);
            $this->getPresenter()->redirect('this');
        }

        $this->getPresenter()->redirect('Home:Rules');
    }
}
