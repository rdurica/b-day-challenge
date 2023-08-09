<?php

declare(strict_types=1);

namespace App\Presenter;

use Rdurica\Core\Presenter\Presenter;
use Rdurica\Core\Presenter\RequireLoggedUser;
use Rdurica\Core\Presenter\SetMdbTemplateLayout;


final class HomePresenter extends Presenter
{
    use SetMdbTemplateLayout;
    use RequireLoggedUser;
}
