<?php declare(strict_types=1);

namespace App\Modules\Admin\User;

use App\Models\UserManager;
use App\Modules\Admin\BasePresenter;
use Contributte\Datagrid\Datagrid;
use App\Helpers\FormHelper;
use App\Modules\Front\FormFactory\FormFactory;

class UserPresenter extends BasePresenter
{
	#[\Nette\DI\Attributes\Inject]
	public UserManager $userManager;

	#[\Nette\DI\Attributes\Inject]
	public FormFactory $formFactory;

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
			->setFilterSelect(['' => 'Vše', 'user' => 'user', 'admin' => 'admin']);

		$grid->addColumnText('is_active', 'Aktivní')
			->setRenderer(function ($item) {
				if ($item->is_active) {
					return \Nette\Utils\Html::el('span')->class('badge bg-success')->setText('Ano');
				}
				return \Nette\Utils\Html::el('span')->class('badge bg-danger')->setText('Ne');
			})
			->setSortable();

		if ($this->getUser()->isInRole('admin')) {

			$grid->addAction('edit', 'Upravit', 'edit')
				->setIcon('bi bi-pencil')
				->setClass('btn btn-xs btn-outline-primary')
				->setRenderCondition(function ($item) {
					$user = $this->getUser();
					if ($user->isInRole('admin') || $user->getId() === $item->id) {
						return true;
					}
					return false;
				});

			$grid->addToolbarButton('add', 'Nový uživatel')
				->setIcon('bi bi-plus-lg')
				->setClass('btn btn-primary');
		}

		$grid->setPagination(true);

		return $grid;
	}

	public function actionEdit(int $id): void
	{
		if (!$this->getUser()->isInRole('admin') && $this->getUser()->getId() !== $id) {
			$this->flashMessage(FormHelper::MSG_NO_PERMISSION_EDIT, 'danger');
			$this->redirect('default');
		}

		$userRow = $this->userManager->getById($id);
		if (!$userRow) {
			$this->error(FormHelper::ERR_USER_NOT_FOUND);
		}

		$defaults = $userRow->toArray();
		$this['userForm']->setDefaults([
			'firstName' => $defaults['first_name'] ?? null,
			'lastName' => $defaults['last_name'] ?? null,
			'userName' => $defaults['username'] ?? null,
			'email' => $defaults['email'] ?? null,
			'phone' => $defaults['phone'] ?? null,
			'role' => $defaults['role'] ?? null,
			'isActive' => $defaults['is_active'] ?? null,
		]);
	}

	public function actionAdd(): void
	{
		if (!$this->getUser()->isInRole('admin')) {
			$this->flashMessage(FormHelper::MSG_ONLY_ADMIN_ADD, 'danger');
			$this->redirect('default');
		}
	}

	protected function createComponentUserForm(): \Nette\Application\UI\Form
	{
		$form = $this->formFactory->createDefaultForm($this->getUser(), true);
		$form->onSuccess[] = [$this, 'userFormSucceeded'];
		return $form;
	}

	public function userFormSucceeded(\Nette\Application\UI\Form $form, \stdClass $data): void
	{
		$id = (int) $this->getParameter('id');

		try {
			$user = $this->getUser();
			if ($id) {
				// EDIT
				if (!$user->isInRole('admin') && $user->getId() !== $id) {
					$this->error(FormHelper::ERR_ACCESS_DENIED);
				}

				$updateData = [
					'username' => $data->userName,
					'first_name' => $data->firstName,
					'last_name' => $data->lastName,
					'email' => $data->email,
					'phone' => $data->phone,
				];

				if (isset($data->role)) {
					$updateData['role'] = $data->role;
				}
				if (isset($data->isActive)) {
					$updateData['is_active'] = $data->isActive;
				}

				if ($data->password) {
					$updateData['password'] = $data->password;
				}

				$this->userManager->update($id, $updateData);
				$this->flashMessage(FormHelper::FLASH_USER_UPDATED, 'success');
			} else {
				// ADD
				if (!$user->isInRole('admin')) {
					$this->error(FormHelper::ERR_ACCESS_DENIED);
				}

				$this->userManager->add(
					$data->userName,
					$data->firstName,
					$data->lastName,
					$data->email,
					$data->phone,
					$data->password
				);
				$this->flashMessage(FormHelper::FLASH_USER_CREATED, 'success');
			}

		} catch (\Exception $e) {
			$form->addError('Chyba: ' . $e->getMessage());
		}

		$this->redirect('default');
	}
}
