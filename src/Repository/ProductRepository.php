<?php

namespace App\Repository;

use App\Entity\Product;
use App\Enums\ProductSort;

class ProductRepository
{
    public function __construct(private \PDO $pdo) {}

    public function save(Product $product): int
    {
        $stmt = $this->pdo->prepare("
                INSERT INTO products (name, price, category_id, status, created_at)
                VALUES (:name, :price, :category_id, :status, NOW())
            ");
        $stmt->execute([
            'name' => $product->name,
            'price' => $product->price,
            'category_id' => $product->categoryId,
            'status' => $product->status ?? null,
        ]);

        $productId = (int)$this->pdo->lastInsertId();

        if (!empty($product->attributes) && is_array($product->attributes)) {
            $stmtAttr = $this->pdo->prepare("
                INSERT INTO attributes (product_id, key, value)
                VALUES (:product_id, :key, :value)
            ");

            foreach ($product->attributes as $key => $value) {
                $stmtAttr->execute([
                    'product_id' => $productId,
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }

        return $productId;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
                SELECT p.id, p.name, p.price, p.status, p.created_at,
                       c.id AS category_id, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = :id
            ");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$product) {
            return null;
        }

        $stmtAttr = $this->pdo->prepare("
                SELECT key, value
                FROM attributes
                WHERE product_id = :id
            ");
        $stmtAttr->execute(['id' => $id]);
        $attributes = [];
        foreach ($stmtAttr->fetchAll(\PDO::FETCH_ASSOC) as $attr) {
            $attributes[$attr['key']] = $attr['value'];
        }

        $product['attributes'] = $attributes;

        return $product;
    }

    public function update(Product $product): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE products
            SET name = :name,
                price = :price,
                status = :status,
                category_id = :category_id
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'status' => $product->status,
            'category_id' => $product->categoryId,
        ]);

        $this->pdo->prepare("DELETE FROM attributes WHERE product_id = :id")
            ->execute(['id' => $product->id]);

        if (!empty($product->attributes)) {
            $stmtAttr = $this->pdo->prepare("
                INSERT INTO attributes (product_id, key, value)
                VALUES (:product_id, :key, :value)
            ");

            foreach ($product->attributes as $key => $value) {
                $stmtAttr->execute([
                    'product_id' => $product->id,
                    'key' => $key,
                    'value' => $value
                ]);
            }
        }
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare("DELETE FROM attributes WHERE product_id = :id")
            ->execute(['id' => $id]);

        $this->pdo->prepare("DELETE FROM products WHERE id = :id")
            ->execute(['id' => $id]);
    }

    public function findAllWithFilters(array $filters, ?string $sort = ''): array
    {
        $allowedSorts = [
            ProductSort::PRICE_ASC->value => 'p.price ASC',
            ProductSort::ALPHABET->value  => 'p.name ASC',
            ProductSort::NEWEST->value    => 'p.created_at DESC',
        ];

        $query = "
            SELECT p.id, p.name, p.price, p.status, p.created_at,
                   c.id AS category_id, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['category_id'])) {
            $query .= " AND p.category_id = :category_id";
            $params['category_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['price_min'])) {
            $query .= " AND p.price >= :price_min";
            $params['price_min'] = (float)$filters['price_min'];
        }

        if (!empty($filters['price_max'])) {
            $query .= " AND p.price <= :price_max";
            $params['price_max'] = (float)$filters['price_max'];
        }

        if ($sort !== null && array_key_exists($sort, $allowedSorts)) {
            $orderBy = $allowedSorts[$sort];
        } else {
            $orderBy = 'p.id DESC';
        }

        $query .= " ORDER BY $orderBy";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $productIds = array_column($products, 'id');

        if (empty($productIds)) {
            return $products;
        }

        $in = implode(',', array_fill(0, count($productIds), '?'));

        $stmt = $this->pdo->prepare("
            SELECT product_id, key, value
            FROM attributes
            WHERE product_id IN ($in)
        ");
        $stmt->execute($productIds);

        $allAttributes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $attributesByProduct = [];
        foreach ($allAttributes as $attr) {
            $attributesByProduct[$attr['product_id']][$attr['key']] = $attr['value'];
        }

        foreach ($products as &$product) {
            $product['attributes'] = $attributesByProduct[$product['id']] ?? [];
        }

        return $products;
    }
}