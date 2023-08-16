<?php

declare(strict_types=1);

namespace App\Component\Grid\TaskCatalogue;

use App\Model\Manager\TaskCatalogueManager;
use Rdurica\Core\Component\Component;
use Rdurica\Core\Component\ComponentRenderer;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

final class TaskCatalogueGrid extends Component
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

        $grid->setDataSource($this->taskCatalogueManager->find());
        $grid->addColumnNumber('id', 'id')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('summary', 'summary')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnDateTime('start_date', 'start_date')
            ->setSortable();
        $grid->addColumnDateTime('due_date', 'due_date')
            ->setSortable();
        $grid->addColumnText('require_photos', 'require_photos')
            ->setSortable()
            ->setTemplateEscaping(false)
            ->setRenderer(function ($row) use ($booleanHtml) {
                return $booleanHtml[$row["require_photos"]];
            });
        $grid->addColumnText('require_video', 'require_video')
            ->setSortable()
            ->setTemplateEscaping(false)
            ->setRenderer(function ($row) use ($booleanHtml) {
                return $booleanHtml[$row["require_video"]];
            });
        $grid->addColumnText('require_text', 'require_text')
            ->setSortable()
            ->setTemplateEscaping(false)
            ->setRenderer(function ($row) use ($booleanHtml) {
                return $booleanHtml[$row["require_text"]];
            });
        $grid->addColumnText('is_enabled', 'is_enabled')
            ->setSortable()
            ->setTemplateEscaping(false)
            ->setRenderer(function ($row) use ($booleanHtml) {
                return $booleanHtml[$row["is_enabled"]];
            });
        $grid->addAction('edit', 'Edit')
            ->setClass('btn btn-sm btn-primary');

        return $grid;
    }

    public function handleEdit(int $id)
    {

    }

}