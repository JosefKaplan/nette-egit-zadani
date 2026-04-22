<?php declare(strict_types=1);

namespace App\Modules\Admin\User;

use App\Models\UserManager;
use App\Modules\Admin\BasePresenter;
use Contributte\Datagrid\Datagrid;
use App\Helpers\FormHelper;

class UserPresenter extends BasePresenter
{
	#[\Nette\DI\Attributes\Inject]
	public UserManager $userManager;

	public function renderDefault(): void
	{
	}

	public function createComponentUsersGrid(string $name): Datagrid
	{
		$grid = new Datagrid($this, $name);

		$grid->setDataSource($this->userManager->findAll());

		$grid->addColumnNumber('id', 'ID')
			->setSortable();

		$grid->addColumnText('username', FormHelper::LABEL_USERNAME)
			->setSortable()
			->setFilterText();

		$grid->addColumnText('first_name', FormHelper::LABEL_FIRST_NAME)
			->setSortable()
			->setFilterText();

		$grid->addColumnText('last_name', FormHelper::LABEL_LAST_NAME)
			->setSortable()
			->setFilterText();

		$grid->addColumnText('email', FormHelper::LABEL_EMAIL)
			->setSortable()
			->setFilterText();

		$grid->addColumnText('phone', FormHelper::LABEL_PHONE);

		$grid->addColumnText('role', 'Role')
			->setSortable()
			->setFilterSelect(['' => 'Vše', 'user' => 'User', 'admin' => 'Admin']);

		$grid->addColumnText('is_active', 'Aktivní')
			->setRenderer(function($item) {
				if ($item->is_active) {
					return \Nette\Utils\Html::el('span')->class('badge bg-success')->setText('Ano');
				}
				return \Nette\Utils\Html::el('span')->class('badge bg-danger')->setText('Ne');
			})
			->setSortable();

		$grid->setPagination(true);

		return $grid;
	}
}
