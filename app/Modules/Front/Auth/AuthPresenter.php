<?php declare(strict_types=1);

namespace App\Modules\Front\Auth;

use App\Modules\Front\BasePresenter;
use Nette\Application\UI\Form;
use App\Helpers\FormHelper;

final class AuthPresenter extends BasePresenter
{

	public function createComponentLoginForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Uživatelské jméno')
			->setRequired(FormHelper::NameRequired)
			->addRule($form::MIN_LENGTH, FormHelper::NameMinLengthMsg, FormHelper::NameMinLengthVal);

		$form->addPassword('password', 'Heslo')
			->setRequired(FormHelper::PasswordRequired)
			->addRule($form::MIN_LENGTH, FormHelper::LoginPasswordMinLengthMsg, FormHelper::LoginPasswordMinLengthVal);

		$form->addCheckbox('remember', 'Zůstat trvale přihlášený');

		$form->addSubmit('submit', 'Přihlásit se');

		$form->onSuccess[] = [$this, 'onLoginFormSuccess'];

		return $form;
	}

	public function onLoginFormSuccess(Form $form, \stdClass $data): void
	{
		try {
			if ($data->remember) {
				$this->getUser()->setExpiration('14 days');
			} else {
				$this->getUser()->setExpiration('1 hour');
			}

			$this->getUser()->login($data->username, $data->password);

			$this->flashMessage('Úspěšné přihlášení.', 'success');
			$this->redirect(':Admin:Base:default');

		} catch (\Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}


	public function renderDefault(): void
	{
		$this->redirect('login');
	}

	public function actionLogin(): void
	{
		$this->checkNotAuthenticated();
	}

	public function actionRegister(): void
	{
		$this->checkNotAuthenticated();
	}
}
