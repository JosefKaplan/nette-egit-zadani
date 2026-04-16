<?php declare(strict_types=1);

namespace App\Modules\Front\Auth;

use App\Modules\Front\BasePresenter;

final class AuthPresenter extends BasePresenter
{
	public function actionLogin(): void
	{
		$this->checkNotAuthenticated();
	}

	public function actionRegister(): void
	{
		$this->checkNotAuthenticated();
	}
}
