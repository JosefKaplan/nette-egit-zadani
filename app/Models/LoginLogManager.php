<?php

declare(strict_types=1);

namespace App\Models;

use Nette\Database\Explorer;

class LoginLogManager
{
	public function __construct(
		private readonly Explorer $explorer,
	) {
	}

	public function logAttempt(string $username, bool $success, string $ipAddress, string $userAgent): void
	{
		$this->explorer->table('login_logs')->insert([
			'username_attempt' => $username,
			'is_success' => (int) $success,
			'ip_address' => $ipAddress,
			'user_agent' => $userAgent,
			'created_at' => new \DateTimeImmutable(),
		]);
	}
}
