<?php

declare(strict_types=1);

namespace App\Component\Grid\TaskCatalogue;

use App\Model\Constant\Resource;
use App\Model\Manager\TaskCatalogueManager;
use Exception;
use Nette\Application\AbortException;
use Nette\Security\User;
use Rdurica\Core\Component\Component;
use Rdurica\Core\Component\ComponentRenderer;
use Rdurica\Core\Constant\FlashType;
use Rdurica\Core\Constant\Privileges;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

/**
 * TaskCatalogueGrid.
 *
 * @package   App\Component\Grid\TaskCatalogue
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class TaskCatalogueGrid extends Component
{
    use ComponentRenderer;

    /**
     * Constructor.
     *
     * @param TaskCatalogueManager $taskCatalogueManager
     * @param User                 $user
     */
    public function __construct(
        private readonly TaskCatalogueManager $taskCatalogueManager,
        private readonly User $user
    ) {
    }

    public function test()
    {
    }

    /**
     * Task catalogue grid.
     *
     * @throws DataGridException
     */
    public function createComponentGrid(): DataGrid
    {
        $grid = new DataGrid($this, 'grid');
        $booleanHtml = [
            false => '<h6><span class="badge badge-danger">Ne</span></h6>',
            true => '<h6><span class="badge badge-success">Ano</span></h6>'
        ];

        $grid->setDataSource($this->taskCatalogueManager->find()->order('id DESC'));
        $grid->addColumnNumber('id', 'Id')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('summary', 'Název')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnDateTime('start_date', 'Začátek')
            ->setSortable();
        $grid->addColumnDateTime('due_date', 'Konec')
            ->setSortable();
        $grid->addColumnText('require_photos', 'Vyžaduje fotky')
            ->setSortable()
            ->setTemplateEscaping(false)
            ->setRenderer(function ($row) use ($booleanHtml) {
                return $booleanHtml[$row["require_photos"]];
            });
        $grid->addColumnText('require_video', 'Vyžaduje video')
            ->setSortable()
            ->setTemplateEscaping(false)
            ->setRenderer(function ($row) use ($booleanHtml) {
                return $booleanHtml[$row["require_video"]];
            });
        $grid->addColumnText('require_text', 'Vyžaduje text')
            ->setSortable()
            ->setTemplateEscaping(false)
            ->setRenderer(function ($row) use ($booleanHtml) {
                return $booleanHtml[$row["require_text"]];
            });
        $grid->addColumnText('is_enabled', 'Povolen')
            ->setSortable()
            ->setTemplateEscaping(false)
            ->setRenderer(function ($row) use ($booleanHtml) {
                return $booleanHtml[$row["is_enabled"]];
            });

        if ($this->user->isAllowed(Resource::TASK_CATALOGUE, Privileges::EDIT)) {
            $grid->addAction('edit', 'Upravit', 'Task:edit')
                ->setClass('btn btn-sm btn-primary');
        }

        if ($this->user->isAllowed(Resource::TASK_CATALOGUE, Privileges::DELETE)) {
            $grid->addAction('delete', 'Smazat', 'delete!')
                ->setClass('btn btn-sm btn-danger');
        }

        return $grid;
    }

    public function handleEdit(int $id): void
    {
    }

    /**
     * @param int $id
     * @return void
     * @throws AbortException
     */
    public function handleDelete(int $id): void
    {
        try {
            $this->taskCatalogueManager->findById($id)->delete();
            $this->getPresenter()->flashMessage('Položka úspěšne smazána', FlashType::SUCCESS);
        } catch (Exception) {
            $this->getPresenter()->flashMessage('Oops. Něco se pokazilo', FlashType::ERROR);
            $this->getPresenter()->redirect('this');
        }

        $this->getPresenter()->redirect('this');
    }

}