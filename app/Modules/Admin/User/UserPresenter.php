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

		$this['userForm']->setDefaults($userRow->toArray());
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
		$form = new \Nette\Application\UI\Form;

		$form->addText('first_name', FormHelper::LABEL_FIRST_NAME)
			->setRequired(FormHelper::REQUIRED_FIRST_NAME);

		$form->addText('last_name', FormHelper::LABEL_LAST_NAME)
			->setRequired(FormHelper::REQUIRED_LAST_NAME);

		$form->addText('username', FormHelper::LABEL_USERNAME)
			->setRequired(FormHelper::REQUIRED_USERNAME);

		$form->addEmail('email', FormHelper::LABEL_EMAIL)
			->setRequired(FormHelper::REQUIRED_EMAIL);

		$form->addText('phone', FormHelper::LABEL_PHONE)
			->setRequired(FormHelper::REQUIRED_PHONE);

		$form->addPassword('password', FormHelper::LABEL_PASSWORD)
			->setNullable()
			->addCondition($form::FILLED)
			->addRule($form::MIN_LENGTH, FormHelper::MSG_REG_PASSWORD_MIN_LENGTH, FormHelper::VAL_REG_PASSWORD_MIN_LENGTH)
			->addRule($form::PATTERN, FormHelper::MSG_REG_PASSWORD_PATTERN, FormHelper::VAL_REG_PASSWORD_PATTERN);

		if ($this->getUser()->isInRole('admin')) {
			$form->addSelect('role', FormHelper::LABEL_ROLE, [
				'user' => 'Uživatel',
				'admin' => 'Administrátor'
			]);
			$form->addCheckbox('is_active', FormHelper::LABEL_IS_ACTIVE);
		}

		$form->addSubmit('submit', FormHelper::LABEL_SUBMIT_USER_SAVE);

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

				$updateData = (array) $data;
				if (!$data->password)
					unset($updateData['password']);

				$this->userManager->update($id, $updateData);
				$this->flashMessage(FormHelper::FLASH_USER_UPDATED, 'success');
			} else {
				// ADD
				if (!$user->isInRole('admin')) {
					$this->error(FormHelper::ERR_ACCESS_DENIED);
				}

				$this->userManager->add(
					$data->username,
					$data->first_name,
					$data->last_name,
					$data->email,
					$data->phone,
					$data->password
				);
				$this->flashMessage(FormHelper::FLASH_USER_CREATED, 'success');
			}

			$this->redirect('default');

		} catch (\Exception $e) {
			$form->addError('Chyba: ' . $e->getMessage());
		}
	}
}
