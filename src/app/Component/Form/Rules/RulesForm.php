<?php

declare(strict_types=1);

namespace App\Component\Form\Rules;

use App\Model\Data\RulesData;
use App\Model\Manager\RulesManager;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
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
     */
    public function __construct(private readonly RulesManager $rulesManager)
    {
    }

    /**
     * Rules form.
     *
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = new Form();
        $form->setMappedType(RulesData::class);
        $form->addTextArea('message')
            ->setDefaultValue($this->rulesManager->findMessage()->message)
            ->setHtmlAttribute('style', 'height: 70vh;')
            ->setRequired();
        $form->addSubmit('save', 'Uložit');

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
            $this->rulesManager->update($data);
            $this->getPresenter()->flashMessage('Pravidla úspěšně upravena', FlashType::SUCCESS);
        } catch (Exception){
            $this->getPresenter()->flashMessage('Oops. Něco se pokazilo', FlashType::ERROR);
            $this->getPresenter()->redirect('this');
        }

        $this->getPresenter()->redirect('Home:Rules');
    }
}
