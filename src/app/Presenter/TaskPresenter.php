<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Component\Form\Task\ITaskForm;
use App\Component\Form\Task\TaskForm;
use App\Component\Grid\TaskCatalogue\ITaskCatalogueGrid;
use App\Component\Grid\TaskCatalogue\TaskCatalogueGrid;
use App\Model\Constant\Resource;
use Nepada\SecurityAnnotations\Annotations\Allowed;
use Nepada\SecurityAnnotations\SecurityAnnotations;
use Nette\DI\Attributes\Inject;
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
    public ITaskForm $taskForm;

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
     * Task catalogue datagrid.
     *
     * @return TaskCatalogueGrid
     */
    protected function createComponentTaskCatalogueGrid(): TaskCatalogueGrid
    {
        return $this->taskCatalogueGrid->create();
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