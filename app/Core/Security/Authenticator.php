<?php declare(strict_types=1);

namespace App\Core\Security;

use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\SimpleIdentity;
use Nette\Security\Passwords;
use Nette\Http\Request;
use App\Models\UserManager;
use App\Models\LoginLogManager;

final class Authenticator implements IAuthenticator
{
	public function __construct(
		private readonly UserManager $userManager,
		private readonly LoginLogManager $loginLogManager,
		private readonly Passwords $passwords,
		private readonly Request $httpRequest,
	) {
	}

	public function authenticate(string $user, string $password): SimpleIdentity
	{
		$ip = $this->httpRequest->getRemoteAddress() ?? 'unknown';
		$userAgent = $this->httpRequest->getHeader('User-Agent') ?? 'unknown';
		$row = $this->userManager->findByUsername($user);

		if (!$row) {
			$this->loginLogManager->logAttempt($user, false, $ip, $userAgent);
			throw new AuthenticationException('Nebylo nalezeno uživatelské jméno.');
		}

		if (!$this->passwords->verify($password, $row->password)) {
			$this->loginLogManager->logAttempt($user, false, $ip, $userAgent);
			throw new AuthenticationException('Zadané heslo není správné.');
		}

		if (!$row->is_active) {
			$this->loginLogManager->logAttempt($user, false, $ip, $userAgent);
			throw new AuthenticationException('Tento účet není aktivní.');
		}
		$this->loginLogManager->logAttempt($user, true, $ip, $userAgent);

		return new SimpleIdentity($row->id, $row->role, ['username' => $row->username, 'name' => $row->first_name . ' ' . $row->last_name]);
	}
}
