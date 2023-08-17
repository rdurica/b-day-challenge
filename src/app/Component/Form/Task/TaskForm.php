<?php

declare(strict_types=1);

namespace App\Component\Form\Task;

use App\Model\Data\TaskCatalogueData;
use App\Model\Manager\TaskCatalogueManager;
use Exception;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Rdurica\Core\Component\Component;
use Rdurica\Core\Component\ComponentRenderer;
use Rdurica\Core\Constant\FlashType;

/**
 * TaskForm
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
     */
    public function __construct(private readonly TaskCatalogueManager $taskCatalogueManager)
    {
    }

    /**
     * Create form.
     *
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $editTaskId = $this->getPresenter()->getParameter('id');

        $form = new Form();
        $form->addText('summary', 'Název')
            ->setMaxLength(100)
            ->setRequired();
        $form->addTextArea('description', 'Popis');
        $form->addText('start_date', 'Začátek')
            ->setRequired();
        $form->addText('due_date', 'Konec')
            ->setRequired();
        $form->addCheckbox('require_photos', 'Vyžaduje fotky');
        $form->addCheckbox('require_video', 'Vyžaduje video');
        $form->addCheckbox('require_text', 'Vyžaduje text');
        $form->addCheckbox('is_enabled', 'Povolen')
            ->setDefaultValue(true);

        $form->addSubmit('save', 'Uložit');

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
     * @param Form              $form
     * @param TaskCatalogueData $data
     * @return void
     */
    public function formOnSuccess(Form $form, ArrayHash $data): void
    {
        $editTaskId = $this->getPresenter()->getParameter('id');
        $data->start_date = new DateTime($data->start_date);
        $data->due_date = new DateTime($data->due_date);

        try {
            $message = 'Úkol úspěšně vytvořen';
            if ($editTaskId) {
                $this->taskCatalogueManager->updateById((int)$editTaskId, $data);
                $message = 'Úkol úspěšně upraven';
            } else {
                $this->taskCatalogueManager->insert($data);
            }
            $this->getPresenter()->flashMessage($message, FlashType::SUCCESS);
        } catch (UniqueConstraintViolationException) {
            $this->getPresenter()->flashMessage('Název úkolu musí být unikátny', FlashType::ERROR);
            $this->getPresenter()->redirect('this');
        } catch (Exception) {
            $this->getPresenter()->flashMessage('Oops. Něco se pokazilo', FlashType::ERROR);
            $this->getPresenter()->redirect('this');
        }

        $this->getPresenter()->redirect('Task:');
    }
}
