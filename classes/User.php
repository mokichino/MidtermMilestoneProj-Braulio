<?php
declare(strict_types=1);

class User
{
    public static function findByEmail(string $email): ?array
    {
        $statement = Database::connection()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();
        return $user ?: null;
    }

    public static function find(int $id): ?array
    {
        $statement = Database::connection()->prepare('SELECT id, full_name, email FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();
        return $user ?: null;
    }

    public static function create(string $name, string $email, string $password): bool
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO users (full_name, email, password_hash) VALUES (:full_name, :email, :password_hash)'
        );
        return $statement->execute([
            'full_name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }
}
