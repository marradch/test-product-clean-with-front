CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TYPE product_status AS ENUM ('available', 'out_of_stock', 'archived');

CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price NUMERIC(10, 2) NOT NULL,
    category_id INT REFERENCES categories(id) ON DELETE SET NULL,
    status product_status NOT NULL DEFAULT 'available',
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE attributes (
    id SERIAL PRIMARY KEY,
    product_id INT REFERENCES products(id) ON DELETE CASCADE,
    key VARCHAR(100) NOT NULL,
    value VARCHAR(255) NOT NULL
);

INSERT INTO categories (name) VALUES
    ('electronics'),
    ('clothing'),
    ('home_appliances')
ON CONFLICT (name) DO NOTHING;

INSERT INTO products (name, price, category_id, status, created_at) VALUES
('Smartphone A1', 499.99, 1, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Laptop Pro 15', 1299.99, 1, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Bluetooth Headphones', 99.99, 1, 'out_of_stock', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('4K TV 55"', 799.99, 1, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Gaming Console X', 399.99, 1, 'archived', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Men T-Shirt', 19.99, 2, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Women Dress', 49.99, 2, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Winter Jacket', 89.99, 2, 'out_of_stock', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Running Shoes', 59.99, 2, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Cap', 14.99, 2, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Microwave Oven', 129.99, 3, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Refrigerator 300L', 599.99, 3, 'out_of_stock', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Washing Machine', 499.99, 3, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Dishwasher', 449.99, 3, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Coffee Maker', 79.99, 3, 'archived', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Vacuum Cleaner', 149.99, 3, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Blender', 49.99, 3, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Toaster', 29.99, 3, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Iron', 39.99, 3, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL),
('Air Conditioner', 699.99, 3, 'available', NOW() - (FLOOR(RANDOM() * 730) || ' days')::INTERVAL);

-- Для Smartphone A1 с id = 1
INSERT INTO attributes (product_id, key, value) VALUES
(1, 'color', 'black'),
(1, 'storage', '64GB');

-- Для Laptop Pro 15 с id = 2
INSERT INTO attributes (product_id, key, value) VALUES
(2, 'processor', 'Intel i7'),
(2, 'ram', '16GB');

-- Для Bluetooth Headphones с id = 3
INSERT INTO attributes (product_id, key, value) VALUES
(3, 'color', 'white'),
(3, 'wireless', 'true');