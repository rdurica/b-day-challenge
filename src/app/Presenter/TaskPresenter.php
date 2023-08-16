<?php

declare(strict_types=1);

namespace App\Presenter;

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

final class TaskPresenter extends Presenter
{
    use RequireLoggedUser;
    use SecurityAnnotations;
    use SetMdbTemplateLayout;

    #[Inject]
    public ITaskCatalogueGrid $taskCatalogueGrid;

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
     * Task catalogue datagrid.
     *
     * @return TaskCatalogueGrid
     */
    protected function createComponentTaskCatalogueGrid(): TaskCatalogueGrid
    {
        return $this->taskCatalogueGrid->create();
    }
}