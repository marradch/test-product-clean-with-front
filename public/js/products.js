$(function() {
    function escapeHtml(text) {
        if (typeof text !== 'string') return text;
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function renderProducts(products) {
        if (!products.length) {
            $('#products-container').html('<p>Товары не найдены.</p>');
            return;
        }

        const html = products.map(product => {
            let attrsHtml = 'Нет';
            if (product.attributes && typeof product.attributes === 'object' && Object.keys(product.attributes).length > 0) {
                attrsHtml = '<ul class="list-unstyled mb-0 small">';
                for (const [key, val] of Object.entries(product.attributes)) {
                    attrsHtml += `<li><strong>${escapeHtml(key)}:</strong> ${escapeHtml(val)}</li>`;
                }
                attrsHtml += '</ul>';
            }

            return `
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${escapeHtml(product.name)}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">${Number(product.price).toFixed(2)} $</h6>
                            <p class="mb-2"><strong>Статус:</strong> ${escapeHtml(product.status)}</p>
                            <p class="mb-2"><strong>Атрибуты:</strong><br />${attrsHtml}</p>
                            <p class="mt-auto text-muted small">Добавлен: ${escapeHtml(product.created_at)}</p>
                            <button type="button" class="btn btn-primary mt-3 buy-btn" data-product='${JSON.stringify(product)}'>
                                Купить
                            </button>
                        </div>
                    </div>
                </div>
        `;
        }).join('');

        $('#products-container').html(html);
    }

    function loadProducts(categoryId, sort) {
        updateUrl(categoryId, sort);
        $.ajax({
            url: '/products',
            method: 'GET',
            data: { category_id: categoryId, sort: sort },
            dataType: 'json',
            success: function(data) {
                renderProducts(data);
                $('#current-category-id').val(categoryId);
                // Обновляем активную категорию
                $('.category-link').removeClass('active');
                $(`.category-link[href$="category_id=${categoryId}"]`).addClass('active');
            },
            error: function() {
                $('#products-container').html('<p>Ошибка загрузки товаров</p>');
            }
        });
    }

    // Обработчик клика по категории
    $('.category-link').on('click', function(e) {
        e.preventDefault();
        const categoryId = new URL($(this).attr('href'), window.location.origin).searchParams.get('category_id');
        const sort = $('#sort-select').val();
        loadProducts(categoryId, sort);
    });

    // Обработчик изменения сортировки
    $('#sort-select').on('change', function() {
        const categoryId = $('#current-category-id').val();
        const sort = $(this).val();
        loadProducts(categoryId, sort);
    });

    function updateUrl(categoryId, sort) {
        const url = new URL(window.location);
        if (categoryId) {
            url.searchParams.set('category_id', categoryId);
        } else {
            url.searchParams.delete('category_id');
        }
        if (sort) {
            url.searchParams.set('sort', sort);
        } else {
            url.searchParams.delete('sort');
        }

        window.history.replaceState(null, '', url.toString());
    }

    $(document).on('click', '.buy-btn', function() {
        const product = $(this).data('product');

        let attrsHtml = '';
        if (product.attributes && typeof product.attributes === 'object' && Object.keys(product.attributes).length > 0) {
            attrsHtml = '<ul>';
            for (const [key, val] of Object.entries(product.attributes)) {
                attrsHtml += `<li><strong>${escapeHtml(key)}:</strong> ${escapeHtml(val)}</li>`;
            }
            attrsHtml += '</ul>';
        } else {
            attrsHtml = '<p>Нет атрибутов</p>';
        }

        const modalHtml = `
            <h5>${escapeHtml(product.name)}</h5>
            <p><strong>Цена:</strong> $${Number(product.price).toFixed(2)}</p>
            <p><strong>Статус:</strong> ${escapeHtml(product.status)}</p>
            <p><strong>Атрибуты:</strong><br />${attrsHtml}</p>
        `;

        $('#modal-product-info').html(modalHtml);

        const buyModal = new bootstrap.Modal(document.getElementById('buyModal'));
        buyModal.show();
    });
});