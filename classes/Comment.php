<?php
declare(strict_types=1);

class Comment
{
    public static function forRecipe(int $recipeId): array
    {
        $statement = Database::connection()->prepare(
            'SELECT c.*, u.full_name AS author_name
             FROM comments c INNER JOIN users u ON u.id = c.user_id
             WHERE c.recipe_id = :recipe_id ORDER BY c.created_at DESC'
        );
        $statement->execute(['recipe_id' => $recipeId]);
        return $statement->fetchAll();
    }

    public static function add(int $recipeId, int $userId, string $text): bool
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO comments (recipe_id, user_id, comment_text) VALUES (:recipe_id, :user_id, :comment_text)'
        );
        return $statement->execute(['recipe_id' => $recipeId, 'user_id' => $userId, 'comment_text' => $text]);
    }

    public static function update(int $id, int $userId, string $text): bool
    {
        $statement = Database::connection()->prepare(
            'UPDATE comments SET comment_text = :comment_text WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute(['comment_text' => $text, 'id' => $id, 'user_id' => $userId]);
        return $statement->rowCount() > 0;
    }

    public static function delete(int $id, int $userId): bool
    {
        $statement = Database::connection()->prepare('DELETE FROM comments WHERE id = :id AND user_id = :user_id');
        $statement->execute(['id' => $id, 'user_id' => $userId]);
        return $statement->rowCount() > 0;
    }
}
