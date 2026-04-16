<?php declare(strict_types=1);

namespace App\Modules\Admin;

use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
	protected function startup(): void
	{
		parent::startup();

		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect(':Front:Auth:login');
		}
	}
}
