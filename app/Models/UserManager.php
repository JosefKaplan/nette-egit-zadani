<?php

declare(strict_types=1);

namespace App\Models;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Security\Passwords;

class UserManager
{
	public function __construct(
		private readonly Explorer $explorer,
		private readonly Passwords $passwords,
	) {
	}

	public function add(string $username, string $firstName, string $lastName, string $email, string $phone, string $password): ActiveRow
	{
		return $this->explorer->table('users')->insert([
			'username' => $username,
			'first_name' => $firstName,
			'last_name' => $lastName,
			'email' => $email,
			'phone' => $phone,
			'password' => $this->passwords->hash($password),
			'role' => 'user', // Výchozí role
			'is_active' => 1,
		]);
	}

	public function findByUsername(string $username): ?ActiveRow
	{
		return $this->explorer->table('users')
			->where('username', $username)
			->fetch();
	}

	public function isDuplicate(string $username, string $email): bool
	{
		return (bool) $this->explorer->table('users')
			->where('username = ? OR email = ?', $username, $email)
			->fetch();
	}

	public function findAll(): \Nette\Database\Table\Selection
	{
		return $this->explorer->table('users');
	}

	public function getById(int $id): ?ActiveRow
	{
		return $this->explorer->table('users')->get($id);
	}

	public function update(int $id, array $data): void
	{
		$row = $this->getById($id);
		if (!$row) {
			throw new \Exception('Uživatel nebyl nalezen.');
		}

		if (isset($data['password'])) {
			$data['password'] = $this->passwords->hash($data['password']);
		}

		$row->update($data);
	}

	public function delete(int $id): void
	{
		$row = $this->getById($id);
		if ($row) {
			$row->delete();
		}
	}
}
