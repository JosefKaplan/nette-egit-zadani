<?php declare(strict_types=1);

namespace App\Modules\Front;

use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
	protected function checkNotAuthenticated(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect(':Admin:User:default');
		}
	}
}
