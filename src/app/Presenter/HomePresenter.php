<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Component\Form\CompleteTask\CompleteTaskForm;
use App\Component\Form\CompleteTask\ICompleteTaskForm;
use App\Component\Form\Rules\IRulesForm;
use App\Component\Form\Rules\RulesForm;
use App\Exception\NewTaskException;
use App\Model\Constant\Resource;
use App\Model\Facade\TaskFacade;
use App\Model\Manager\RulesManager;
use Nepada\SecurityAnnotations\Annotations\Allowed;
use Nepada\SecurityAnnotations\SecurityAnnotations;
use Nette\Application\AbortException;
use Nette\Database\UniqueConstraintViolationException;
use Nette\DI\Attributes\Inject;
use Rdurica\Core\Constant\FlashType;
use Rdurica\Core\Constant\Privileges;
use Rdurica\Core\Presenter\Presenter;
use Rdurica\Core\Presenter\RequireLoggedUser;
use Rdurica\Core\Presenter\SetMdbTemplateLayout;

/**
 * HomePresenter.
 *
 * @package   App\Presenter
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class HomePresenter extends Presenter
{
    use SetMdbTemplateLayout;
    use RequireLoggedUser;
    use SecurityAnnotations;

    #[Inject]
    public RulesManager $rulesManager;

    #[Inject]
    public IRulesForm $rulesForm;

    #[Inject]
    public ICompleteTaskForm $completeTaskForm;

    #[Inject]
    public TaskFacade $taskFactory;

    /** @var string Presenter name. */
    public const PRESENTER_NAME = 'Home';

    /**
     * Home page.
     *
     * @return void
     * @throws AbortException
     */
    public function renderDefault(): void
    {
        $data = $this->taskFactory->prepareHomeDefaultData();
        $this->getPresenter()->template->data = $data;

        if ($data->expiredOldTask) {
            $this->flashMessage('Nestich si splnit ukol v danom limite.', FlashType::WARNING);
            $this->getPresenter()->redirect('this');
        }
    }

    /**
     * Rules page.
     *
     * @return void
     */
    #[Allowed(resource: Resource::RULES, privilege: Privileges::VIEW)]
    public function renderRules(): void
    {
        $rulesData = $this->rulesManager->findMessage();
        $this->getTemplate()->rulesData = $rulesData;
    }

    /**
     * Edit rules page.
     *
     * @return void
     */
    #[Allowed(resource: Resource::RULES, privilege: Privileges::EDIT)]
    public function renderRulesEdit(): void
    {
    }

    public function renderFinishTask(int $taskAssignedId): void
    {
    }

    /**
     * Start new task.
     *
     * @return void
     * @throws AbortException
     */
    public function handleStartTask(): void
    {
        try {
            $this->taskFactory->assignTask();
            $this->flashMessage('Ukol uspesne zapocal.', FlashType::SUCCESS);
        } catch (UniqueConstraintViolationException) {
            $this->flashMessage('Ukol jiz existuje.', FlashType::ERROR);
        } catch (NewTaskException $e) {
            $this->flashMessage($e->getMessage(), FlashType::WARNING);
        }

        $this->getPresenter()->redirect('this');
    }


    /**
     * Rules form component.
     *
     * @return RulesForm
     */
    protected function createComponentRulesForm(): RulesForm
    {
        return $this->rulesForm->create();
    }


    protected function createComponentCompleteTaskForm(): CompleteTaskForm
    {
        return $this->completeTaskForm->create();
    }
}
