<?php declare(strict_types=1);

namespace App\Modules\Front\FormFactory;

use Nette\Application\UI\Form;
use Nette\Security\User;
use App\Helpers\FormHelper;

class FormFactory
{
    public function createDefaultForm(User $user, bool $adminCheck, bool $login = false): Form
    {
        $form = new Form;

        if (!$login) {
            $form->addText('firstName', FormHelper::LABEL_FIRST_NAME)
                ->setRequired(FormHelper::REQUIRED_FIRST_NAME);

            $form->addText('lastName', FormHelper::LABEL_LAST_NAME)
                ->setRequired(FormHelper::REQUIRED_LAST_NAME);
        }

        $userName = $form->addText('userName', FormHelper::LABEL_USERNAME)
            ->setRequired(FormHelper::REQUIRED_USERNAME);

        if (!$login) {
            $userName->addRule($form::MIN_LENGTH, FormHelper::MSG_NAME_MIN_LENGTH, FormHelper::VAL_NAME_MIN_LENGTH);
        }

        if (!$login) {
            $form->addText('email', FormHelper::LABEL_EMAIL)
                ->setRequired(FormHelper::REQUIRED_EMAIL)
                ->addRule($form::EMAIL, FormHelper::MSG_EMAIL_INVALID);

            $form->addText('phone', FormHelper::LABEL_PHONE)
                ->setRequired(FormHelper::REQUIRED_PHONE)
                ->addRule($form::PATTERN, FormHelper::MSG_PHONE_PATTERN, FormHelper::VAL_PHONE_PATTERN);
        }

        $password = $form->addPassword('password', FormHelper::LABEL_PASSWORD_SECURE)
            ->setRequired(FormHelper::REQUIRED_PASSWORD_SECURE);

        if (!$login) {
            $password->addRule($form::MIN_LENGTH, FormHelper::MSG_REG_PASSWORD_MIN_LENGTH, FormHelper::VAL_REG_PASSWORD_MIN_LENGTH)
                ->addRule($form::PATTERN, FormHelper::MSG_REG_PASSWORD_PATTERN, FormHelper::VAL_REG_PASSWORD_PATTERN);
        }

        if (!$login && $adminCheck) {
            if ($user->isInRole('admin')) {
                $form->addSelect('role', FormHelper::LABEL_ROLE, [
                    'user' => 'Uživatel',
                    'admin' => 'Administrátor'
                ]);
                $form->addCheckbox('isActive', FormHelper::LABEL_IS_ACTIVE);
            }
        }

        if ($login) {
            $form->addCheckbox('remember', FormHelper::LABEL_REMEMBER);
        }

        $form->addSubmit('submit', FormHelper::LABEL_SUBMIT_USER_SAVE);

        return $form;
    }
}