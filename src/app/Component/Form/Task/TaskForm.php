<?php

declare(strict_types=1);

namespace App\Component\Form\Task;

use App\Model\Manager\TaskCatalogueManager;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Rdurica\Core\Component\Component;
use Rdurica\Core\Component\ComponentRenderer;
use Rdurica\Core\Constant\FlashType;

/**
 * TaskForm.
 *
 * @package   App\Component\Form\Task
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class TaskForm extends Component
{
    use ComponentRenderer;

    /**
     * Constructor.
     *
     * @param TaskCatalogueManager $taskCatalogueManager
     * @param Translator           $translator
     */
    public function __construct(
        private readonly TaskCatalogueManager $taskCatalogueManager,
        private readonly Translator $translator
    ) {
    }

    /**
     * Create form.
     *
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $editTaskId = $this->getPresenter()->getParameter('id');
        $labelTask = $this->translator->translate('labels.task');
        $labelStartDate = $this->translator->translate('labels.startDate');
        $labelEndDate = $this->translator->translate('labels.endDate');
        $labelButton = $this->translator->translate('labels.save');

        $form = new Form();
        $form->addText('summary', $labelTask)
            ->setMaxLength(100)
            ->setRequired();
        $form->addTextArea('description');
        $form->addText('start_date', $labelStartDate)
            ->setRequired();
        $form->addText('due_date', $labelEndDate)
            ->setRequired();
        $form->addCheckbox('require_photos');
        $form->addCheckbox('require_video');
        $form->addCheckbox('require_text');
        $form->addCheckbox('is_enabled')
            ->setDefaultValue(true);

        $form->addSubmit('save', $labelButton);

        $form->onSuccess[] = [$this, 'formOnSuccess'];

        if ($editTaskId) {
            $data = $this->taskCatalogueManager->findById((int)$editTaskId);
            $form->setDefaults($data);
            $form->setDefaults([
                'start_date' => $data->start_date->format('m/d/Y'),
                'due_date' => $data->due_date->format('m/d/Y'),
            ]);
        }

        return $form;
    }

    /**
     * Process form.
     *
     * @param Form      $form
     * @param ArrayHash $data
     * @return void
     * @throws AbortException
     * @throws Exception
     */
    public function formOnSuccess(Form $form, ArrayHash $data): void
    {
        $editTaskId = $this->getPresenter()->getParameter('id');
        $data->start_date = new DateTime($data->start_date);
        $data->due_date = new DateTime($data->due_date);

        try {
            $message = $this->translator->translate('messages.success.taskCreated');
            if ($editTaskId) {
                $this->taskCatalogueManager->updateById((int)$editTaskId, $data);
                $message = $this->translator->translate('messages.success.taskUpdated');
            } else {
                $this->taskCatalogueManager->insert($data);
            }
            $this->getPresenter()->flashMessage($message, FlashType::SUCCESS);
        } catch (UniqueConstraintViolationException) {
            $message = $this->translator->translate('messages.error.uniqueTaskName');
            $this->getPresenter()->flashMessage($message, FlashType::ERROR);
            $this->getPresenter()->redirect('this');
        } catch (Exception) {
            $message = $this->translator->translate('messages.error.generalError');
            $this->getPresenter()->flashMessage($message, FlashType::ERROR);
            $this->getPresenter()->redirect('this');
        }

        $this->getPresenter()->redirect('Task:');
    }
}
