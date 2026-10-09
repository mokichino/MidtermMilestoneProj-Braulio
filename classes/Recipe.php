<?php
declare(strict_types=1);

class Recipe
{
    public static function categories(): array
    {
        return Database::connection()->query(
            'SELECT id, name, unit_options FROM categories ORDER BY name'
        )->fetchAll();
    }

    public static function addCategoryUnit(int $categoryId, string $unit): string
    {
        $database = Database::connection();
        $findCategory = $database->prepare(
            'SELECT unit_options FROM categories WHERE id = :category_id LIMIT 1'
        );
        $findCategory->execute(['category_id' => $categoryId]);
        $category = $findCategory->fetch();

        if (!$category) {
            return 'not_found';
        }

        $units = json_decode((string) ($category['unit_options'] ?? '[]'), true);
        $units = is_array($units) ? $units : [];

        foreach ($units as $existingUnit) {
            if (strcasecmp((string) $existingUnit, $unit) === 0) {
                return 'duplicate';
            }
        }

        $units[] = $unit;
        sort($units, SORT_NATURAL | SORT_FLAG_CASE);

        $updateCategory = $database->prepare(
            'UPDATE categories SET unit_options = :unit_options WHERE id = :category_id'
        );
        return $updateCategory->execute([
            'unit_options' => json_encode(array_values($units), JSON_UNESCAPED_UNICODE),
            'category_id' => $categoryId,
        ]) ? 'added' : 'not_saved';
    }

    public static function latest(string $search = '', ?int $categoryId = null, ?int $userId = null): array
    {
        $sql = 'SELECT r.*, c.name AS category_name, u.full_name AS author_name,
                       (SELECT COUNT(*) FROM favorites f WHERE f.recipe_id = r.id AND f.user_id = :favorite_user) AS is_favorite
                FROM recipes r INNER JOIN categories c ON c.id = r.category_id
                INNER JOIN users u ON u.id = r.user_id WHERE 1 = 1';
        $params = ['favorite_user' => $userId ?? 0];
        if ($search !== '') {
            $sql .= ' AND (r.title LIKE :search OR r.short_description LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }
        if ($categoryId !== null) {
            $sql .= ' AND r.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }
        $sql .= ' ORDER BY r.created_at DESC';
        $statement = Database::connection()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT r.*, c.name AS category_name, u.full_name AS author_name
             FROM recipes r INNER JOIN categories c ON c.id = r.category_id
             INNER JOIN users u ON u.id = r.user_id WHERE r.id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $recipe = $statement->fetch();
        return $recipe ?: null;
    }

    public static function ingredients(int $recipeId): array
    {
        $statement = Database::connection()->prepare('SELECT * FROM ingredients WHERE recipe_id = :recipe_id ORDER BY ingredient_order, id');
        $statement->execute(['recipe_id' => $recipeId]);
        return $statement->fetchAll();
    }

    public static function create(int $userId, int $categoryId, string $title, string $description, string $instructions, array $ingredients): int
    {
        $database = Database::connection();
        $database->beginTransaction();
        try {
            $statement = $database->prepare('INSERT INTO recipes (user_id, category_id, title, short_description, instructions) VALUES (:user_id, :category_id, :title, :description, :instructions)');
            $statement->execute(['user_id' => $userId, 'category_id' => $categoryId, 'title' => $title, 'description' => $description, 'instructions' => $instructions]);
            $recipeId = (int) $database->lastInsertId();
            self::replaceIngredients($recipeId, $ingredients, $database);
            $database->commit();
            return $recipeId;
        } catch (Throwable $error) {
            $database->rollBack();
            throw $error;
        }
    }

    public static function update(int $id, int $userId, int $categoryId, string $title, string $description, string $instructions, array $ingredients): bool
    {
        $database = Database::connection();
        $database->beginTransaction();
        try {
            $statement = $database->prepare('UPDATE recipes SET category_id = :category_id, title = :title, short_description = :description, instructions = :instructions WHERE id = :id AND user_id = :user_id');
            $statement->execute(['category_id' => $categoryId, 'title' => $title, 'description' => $description, 'instructions' => $instructions, 'id' => $id, 'user_id' => $userId]);
            if ($statement->rowCount() === 0 && !self::ownedBy($id, $userId)) {
                $database->rollBack();
                return false;
            }
            self::replaceIngredients($id, $ingredients, $database);
            $database->commit();
            return true;
        } catch (Throwable $error) {
            $database->rollBack();
            throw $error;
        }
    }

    public static function delete(int $id, int $userId): bool
    {
        $statement = Database::connection()->prepare('DELETE FROM recipes WHERE id = :id AND user_id = :user_id');
        $statement->execute(['id' => $id, 'user_id' => $userId]);
        return $statement->rowCount() > 0;
    }

    public static function ownedBy(int $id, int $userId): bool
    {
        $statement = Database::connection()->prepare('SELECT id FROM recipes WHERE id = :id AND user_id = :user_id');
        $statement->execute(['id' => $id, 'user_id' => $userId]);
        return (bool) $statement->fetchColumn();
    }

    private static function replaceIngredients(int $recipeId, array $ingredients, PDO $database): void
    {
        $database->prepare('DELETE FROM ingredients WHERE recipe_id = :recipe_id')->execute(['recipe_id' => $recipeId]);
        $statement = $database->prepare('INSERT INTO ingredients (recipe_id, quantity, unit, ingredient_name, ingredient_order) VALUES (:recipe_id, :quantity, :unit, :ingredient_name, :ingredient_order)');
        foreach ($ingredients as $position => $ingredient) {
            $statement->execute(['recipe_id' => $recipeId, 'quantity' => $ingredient['quantity'], 'unit' => $ingredient['unit'], 'ingredient_name' => $ingredient['name'], 'ingredient_order' => $position + 1]);
        }
    }
}
