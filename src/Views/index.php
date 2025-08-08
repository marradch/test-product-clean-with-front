<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Категории и товары</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<header class="navbar navbar-expand navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">Магазин</a>
    </div>
</header>

<div class="container-fluid" style="padding-top: 56px; padding-bottom: 56px;">
    <div class="row">

        <!-- Сайдбар категории -->
        <nav class="col-md-3 bg-light border-end vh-100 p-3">
            <h5>Категории</h5>
            <div class="list-group">
                <?php foreach ($categories as $category): ?>
                    <?php
                    $active = (isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) ? 'active' : '';
                    ?>
                    <a
                            href="?category_id=<?= htmlspecialchars($category['id']) ?>"
                            class="category-link list-group-item list-group-item-action <?= $active ?>"
                            aria-current="<?= $active ? 'true' : 'false' ?>"
                    >
                        <?= htmlspecialchars($category['name']) ?>
                        <span class="badge bg-secondary rounded-pill">
                    <?= (int)$category['product_count'] ?>
                </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>

        <!-- Основной контент -->
        <main class="col-md-9 pt-3">

            <?php
            $productsExist = !empty($products);
            $sort = $_GET['sort'] ?? 'price_asc';
            ?>

            <!-- Сортировка (только если есть продукты) -->
            <?php if ($productsExist): ?>
                <?php if (isset($_GET['category_id'])): ?>
                    <input type="hidden" id="current-category-id" name="category_id" value="<?= (int)$_GET['category_id'] ?>" />
                <?php endif; ?>

                <label for="sort-select" class="form-label mb-0">Сортировать:</label>
                <select name="sort" id="sort-select" class="form-select">
                    <?php foreach ($options as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= $sort === $value ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <!-- Товары -->
            <?php if ($productsExist): ?>
                <div id="products-container" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 mt-3">
                    <?php foreach ($products as $product): ?>
                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?= number_format($product['price'], 2) ?> $</h6>
                                    <p class="mb-2">
                                        <strong>Статус:</strong> <?= htmlspecialchars($product['status']) ?>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Атрибуты:</strong><br />
                                        <?php if (!empty($product['attributes']) && is_array($product['attributes'])): ?>
                                    <ul class="list-unstyled mb-0 small">
                                        <?php foreach ($product['attributes'] as $key => $val): ?>
                                            <li><strong><?= htmlspecialchars($key) ?>:</strong> <?= htmlspecialchars($val) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php else: ?>
                                        Нет
                                    <?php endif; ?>
                                    </p>
                                    <p class="mt-auto text-muted small">
                                        Добавлен: <?= htmlspecialchars($product['created_at']) ?>
                                    </p>
                                    <button type="button" class="btn btn-primary mt-3 buy-btn" data-product="<?= htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8') ?>">
                                        Купить
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Товары не найдены.</p>
            <?php endif; ?>

        </main>

    </div>
</div>

<footer class="bg-dark text-white text-center py-3 fixed-bottom">
    &copy; <?= date('Y') ?> Магазин. Все права защищены.
</footer>

<!-- Модалка Bootstrap -->
<div class="modal fade" id="buyModal" tabindex="-1" aria-labelledby="buyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buyModalLabel">Купити товар</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрити"></button>
            </div>
            <div class="modal-body">
                <!-- Тут буде інформація про товар -->
                <div id="modal-product-info"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
                <button type="button" class="btn btn-primary">Підтвердити покупку</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle JS (Popper.js + Bootstrap JS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="js/products.js"></script>

</body>
</html>