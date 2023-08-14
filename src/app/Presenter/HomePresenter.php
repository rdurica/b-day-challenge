<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Model\Constant\Resource;
use App\Model\Manager\RulesManager;
use Nepada\SecurityAnnotations\Annotations\Allowed;
use Nepada\SecurityAnnotations\SecurityAnnotations;
use Nette\DI\Attributes\Inject;
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

    /**
     * Rules page.
     *
     * @return void
     */
    #[Allowed(resource: Resource::RULES, privilege: Privileges::VIEW)]
    public function renderRules(): void
    {
        $data = $this->rulesManager->findRules();
        $this->getTemplate()->data = $data;
    }
}
