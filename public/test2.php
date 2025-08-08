<?php

$pdo = new PDO(
    sprintf(
        '%s:host=%s;port=%s;dbname=%s',
        'pgsql',
        'db',
        '5432',
        'test_laravel'
    ),
    'postgres',
    '123456',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$start = microtime(true);

// 1. Построчно читаем и группируем по parent_id
$by_parent = [];

$stmt = $pdo->query("SELECT categories_id, parent_id FROM categories2");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $by_parent[$row['parent_id']][] = $row['categories_id'];
}

// 2. Рекурсивная функция сборки дерева
function build_tree(int $parent_id, array &$by_parent): array {
    if (!isset($by_parent[$parent_id])) {
        return [];
    }

    $result = [];

    foreach ($by_parent[$parent_id] as $child_id) {
        if (isset($by_parent[$child_id])) {
            $result[$child_id] = build_tree($child_id, $by_parent);
        } else {
            $result[$child_id] = $child_id;
        }

        unset($by_parent[$child_id]); // экономия памяти
    }

    return $result;
}

// 3. Строим дерево с корня (где parent_id = 0)
$tree = build_tree(0, $by_parent);

// 4. Выводим
echo '<pre>';
//print_r($tree);
echo '</pre>';

echo "Время выполнения: " . round(microtime(true) - $start, 4) . " сек\n";
