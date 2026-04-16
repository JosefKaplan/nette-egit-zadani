<?php

declare(strict_types=1);

namespace App\Helpers;

final class FormHelper
{
	public const NameRequired = 'Zadejte prosím své uživatelské jméno.';
	public const NameMinLengthMsg = 'Uživatelské jméno musí mít alespoň %d znaky.';
	public const NameMinLengthVal = 3;
	public const PasswordRequired = 'Zadejte prosím své heslo.';
	public const LoginPasswordMinLengthMsg = 'Heslo musí mít alespoň %d znaků.';
	public const LoginPasswordMinLengthVal = 6;
	public const RegisterPasswordMissing = 'Pro registraci musíte zadat bezpečné heslo.';
	public const RegisterPasswordPattern = 'Bezpečné heslo pro přihlášení musí mít alespoň 8 znaků a jednu číslici.';
	public const FirstNameRequired = 'Vyplňte prosím křestní jméno.';
	public const LastNameRequired = 'Vyplňte prosím příjmení.';
	public const EmailRequired = 'Zadejte e-mailovou adresu.';
	public const EmailInvalid = 'Sakra, tohle nevypadá jako správný e-mailový formát.';
	public const PhoneRequired = 'Potřebujeme znát i váš telefon.';
	public const PhoneInvalid = 'Zadejte telefonní číslo ve validním formátu (např. +420 123 456 789).';
}
