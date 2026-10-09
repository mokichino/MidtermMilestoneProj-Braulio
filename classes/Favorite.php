<?php
declare(strict_types=1);

class Favorite
{
    public static function add(int $userId, int $recipeId): bool
    {
        $statement = Database::connection()->prepare(
            'INSERT IGNORE INTO favorites (user_id, recipe_id) VALUES (:user_id, :recipe_id)'
        );
        $statement->execute(['user_id' => $userId, 'recipe_id' => $recipeId]);
        return $statement->rowCount() > 0;
    }

    public static function remove(int $userId, int $recipeId): bool
    {
        $statement = Database::connection()->prepare(
            'DELETE FROM favorites WHERE user_id = :user_id AND recipe_id = :recipe_id'
        );
        $statement->execute(['user_id' => $userId, 'recipe_id' => $recipeId]);
        return $statement->rowCount() > 0;
    }

    public static function exists(int $userId, int $recipeId): bool
    {
        $statement = Database::connection()->prepare(
            'SELECT 1 FROM favorites WHERE user_id = :user_id AND recipe_id = :recipe_id'
        );
        $statement->execute(['user_id' => $userId, 'recipe_id' => $recipeId]);
        return (bool) $statement->fetchColumn();
    }

    public static function forUser(int $userId): array
    {
        $statement = Database::connection()->prepare(
            'SELECT r.*, c.name AS category_name, u.full_name AS author_name
             FROM favorites f INNER JOIN recipes r ON r.id = f.recipe_id
             INNER JOIN categories c ON c.id = r.category_id
             INNER JOIN users u ON u.id = r.user_id
             WHERE f.user_id = :user_id ORDER BY f.created_at DESC'
        );
        $statement->execute(['user_id' => $userId]);
        return $statement->fetchAll();
    }
}
