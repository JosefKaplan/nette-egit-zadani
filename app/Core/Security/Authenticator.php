<?php declare(strict_types=1);

namespace App\Core\Security;

use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\SimpleIdentity;


/**
 * Authenticator – jakmile entities/DB layer is set up.
 */
final class Authenticator implements IAuthenticator
{
	public function authenticate(string $user, string $password): SimpleIdentity
	{
		// TODO: implement DB-based authentication
		throw new AuthenticationException('Not implemented yet.');
	}
}
