<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Component\Form\Task\ITaskForm;
use App\Component\Form\Task\TaskForm;
use App\Component\Grid\TaskCatalogue\ITaskCatalogueGrid;
use App\Component\Grid\TaskCatalogue\TaskCatalogueGrid;
use App\Component\Grid\TaskEvaluation\ITaskEvaluationGrid;
use App\Component\Grid\TaskEvaluation\TaskEvaluationGrid;
use App\Exception\NoResultException;
use App\Model\Constant\Resource;
use App\Model\Facade\TaskFacade;
use Nepada\SecurityAnnotations\Annotations\Allowed;
use Nepada\SecurityAnnotations\SecurityAnnotations;
use Nette\Application\AbortException;
use Nette\DI\Attributes\Inject;
use Nette\InvalidStateException;
use Rdurica\Core\Constant\FlashType;
use Rdurica\Core\Constant\Privileges;
use Rdurica\Core\Presenter\Presenter;
use Rdurica\Core\Presenter\RequireLoggedUser;
use Rdurica\Core\Presenter\SetMdbTemplateLayout;

/**
 * TaskPresenter.
 *
 * @package   App\Presenter
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class TaskPresenter extends Presenter
{
    use RequireLoggedUser;
    use SecurityAnnotations;
    use SetMdbTemplateLayout;

    #[Inject]
    public ITaskCatalogueGrid $taskCatalogueGrid;

    #[Inject]
    public ITaskEvaluationGrid $taskEvaluationGrid;

    #[Inject]
    public TaskFacade $facade;

    #[Inject]
    public ITaskForm $taskForm;

    /** @var string Presenter name. */
    public const PRESENTER_NAME = 'Task';

    /** @var string Render action for evaluation of results. */
    public const ACTION_EVALUATE = 'evaluate';

    /** @var string Action reject assigned task in evaluation phase. */
    public const ACTION_REJECT_TASK = 'taskReject';

    /** @var string Action accept assigned task  in evaluation phase. */
    public const ACTION_ACCEPT_TASK = 'taskAccept';

    /**
     * Reject assigned task.
     *
     * @param int $id
     * @return void
     * @throws AbortException
     */
    #[Allowed(resource: Resource::TASK_EVALUATION, privilege: Privileges::VIEW)]
    public function actionTaskReject(int $id): void
    {
        $this->facade->rejectTask($id);
        $this->flashMessage('Ukol uspesne zamitnut bez nahradniho ukolu.', FlashType::SUCCESS);
        $this->redirect('Task:evaluation');
    }

    /**
     * Accept assigned task.
     *
     * @param int $id
     * @return void
     * @throws AbortException
     */
    #[Allowed(resource: Resource::TASK_EVALUATION, privilege: Privileges::VIEW)]
    public function actionTaskAccept(int $id): void
    {
        $this->facade->acceptTask($id);
        $this->flashMessage('Ukol uspesne akceptovan.', FlashType::SUCCESS);
        $this->redirect('Task:evaluation');
    }

    /**
     * List of all tasks in catalogue.
     *
     * @return void
     */
    #[Allowed(resource: Resource::TASK_CATALOGUE, privilege: Privileges::VIEW)]
    public function renderDefault(): void
    {
    }

    /**
     * Create new task.
     *
     * @return void
     */
    #[Allowed(resource: Resource::TASK_CATALOGUE, privilege: Privileges::CREATE)]
    public function renderCreate(): void
    {
    }

    /**
     * Edit existing task.
     *
     * @param int $id
     * @return void
     */
    #[Allowed(resource: Resource::TASK_CATALOGUE, privilege: Privileges::EDIT)]
    public function renderEdit(int $id): void
    {
    }

    /**
     * Validate finished tasks.
     *
     * @return void
     */
    #[Allowed(resource: Resource::TASK_EVALUATION, privilege: Privileges::VIEW)]
    public function renderEvaluation(): void
    {
    }

    /**
     * Validate finished tasks.
     *
     * @param int $id
     * @return void
     * @throws AbortException
     */
    #[Allowed(resource: Resource::TASK_EVALUATION, privilege: Privileges::EDIT)]
    public function renderEvaluate(int $id): void
    {
        try {
            $assignedTask = $this->facade->prepareEvaluateData($id);
            $this->getTemplate()->assignedTask = $assignedTask;
        } catch (NoResultException|InvalidStateException $e) {
            $this->flashMessage($e->getMessage(), FlashType::ERROR);
            $this->redirect('Home:');
        }
    }

    /**
     * Task catalogue datagrid.
     *
     * @return TaskCatalogueGrid
     */
    protected function createComponentTaskCatalogueGrid(): TaskCatalogueGrid
    {
        return $this->taskCatalogueGrid->create();
    }

    /**
     * Task evaluation datagrid.
     *
     * @return TaskEvaluationGrid
     */
    protected function createComponentTaskEvaluationGrid(): TaskEvaluationGrid
    {
        return $this->taskEvaluationGrid->create();
    }

    /**
     * Task form component.
     *
     * @return TaskForm
     */
    protected function createComponentTaskForm(): TaskForm
    {
        return $this->taskForm->create();
    }
}