<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Component\Form\Rules\IRulesForm;
use App\Component\Form\Rules\RulesForm;
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

    #[Inject]
    public IRulesForm $rulesForm;

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

    /**
     * Rules form component.
     *
     * @return RulesForm
     */
    protected function createComponentRulesForm(): RulesForm
    {
        return $this->rulesForm->create();
    }
}
