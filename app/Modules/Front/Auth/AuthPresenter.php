<?php declare(strict_types=1);

namespace App\Modules\Front\Auth;

use App\Modules\Front\BasePresenter;
use Nette\Application\UI\Form;
use App\Helpers\FormHelper;

class AuthPresenter extends BasePresenter
{
	#[\Nette\DI\Attributes\Inject]
	public \App\Models\UserManager $userManager;

	public function createComponentLoginForm(): Form
	{
		$form = new Form;
		$form->addText('username', FormHelper::LABEL_USERNAME)
			->setRequired(FormHelper::REQUIRED_USERNAME)
			->addRule($form::MIN_LENGTH, FormHelper::MSG_NAME_MIN_LENGTH, FormHelper::VAL_NAME_MIN_LENGTH);

		$form->addPassword('password', FormHelper::LABEL_PASSWORD)
			->setRequired(FormHelper::REQUIRED_PASSWORD)
			->addRule($form::MIN_LENGTH, FormHelper::MSG_LOGIN_PASSWORD_MIN_LENGTH, FormHelper::VAL_LOGIN_PASSWORD_MIN_LENGTH);

		$form->addCheckbox('remember', FormHelper::LABEL_REMEMBER);

		$form->addSubmit('submit', FormHelper::LABEL_SUBMIT_LOGIN);

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

			$this->flashMessage(FormHelper::FLASH_LOGIN_SUCCESS, 'success');
			$this->redirect(':Admin:User:default');

		} catch (\Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

	public function createComponentRegisterForm(): Form
	{
		$form = new Form;

		$form->addText('firstName', FormHelper::LABEL_FIRST_NAME)
			->setRequired(FormHelper::REQUIRED_FIRST_NAME);

		$form->addText('lastName', FormHelper::LABEL_LAST_NAME)
			->setRequired(FormHelper::REQUIRED_LAST_NAME);

		$form->addText('username', FormHelper::LABEL_USERNAME)
			->setRequired(FormHelper::REQUIRED_USERNAME)
			->addRule($form::MIN_LENGTH, FormHelper::MSG_NAME_MIN_LENGTH, FormHelper::VAL_NAME_MIN_LENGTH);

		$form->addEmail('email', FormHelper::LABEL_EMAIL)
			->setRequired(FormHelper::REQUIRED_EMAIL)
			->addRule($form::EMAIL, FormHelper::MSG_EMAIL_INVALID);

		$form->addText('phone', FormHelper::LABEL_PHONE)
			->setRequired(FormHelper::REQUIRED_PHONE)
			->addRule($form::PATTERN, FormHelper::MSG_PHONE_PATTERN, FormHelper::VAL_PHONE_PATTERN);

		$form->addPassword('password', FormHelper::LABEL_PASSWORD_SECURE)
			->setRequired(FormHelper::REQUIRED_PASSWORD_SECURE)
			->addRule($form::MIN_LENGTH, FormHelper::MSG_REG_PASSWORD_MIN_LENGTH, FormHelper::VAL_REG_PASSWORD_MIN_LENGTH)
			->addRule($form::PATTERN, FormHelper::MSG_REG_PASSWORD_PATTERN, FormHelper::VAL_REG_PASSWORD_PATTERN);

		$form->addSubmit('submit', FormHelper::LABEL_SUBMIT_REGISTER);

		$form->onSuccess[] = [$this, 'onRegisterFormSuccess'];

		return $form;
	}

	public function onRegisterFormSuccess(Form $form, \stdClass $data): void
	{
		if ($this->userManager->isDuplicate($data->username, $data->email)) {
			$form->addError(FormHelper::ERR_DUPLICATE_USER);
			return;
		}

		try {
			$this->userManager->add(
				$data->username,
				$data->firstName,
				$data->lastName,
				$data->email,
				$data->phone,
				$data->password
			);
		} catch (\Exception $e) {
			$form->addError(FormHelper::ERR_REGISTRATION);
			return;
		}

		$this->flashMessage(FormHelper::FLASH_REGISTRATION_SUCCESS, 'success');
		$this->redirect('login');
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

	public function actionLogout(): void
	{
		$this->getUser()->logout();
		$this->flashMessage(FormHelper::FLASH_LOGOUT_SUCCESS, 'info');
		$this->redirect('login');
	}
}
