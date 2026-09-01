-- ============================================================
-- LEISURE LOOP TRIP — Local Database Setup
-- ============================================================

CREATE DATABASE IF NOT EXISTS leisure_loop_db;
USE leisure_loop_db;

-- ─── SETTINGS ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hero_type ENUM('image','video') DEFAULT 'video',
    hero_url VARCHAR(500) DEFAULT 'https://cdn.pixabay.com/video/2025/05/06/277097_large.mp4',
    hero_text_main VARCHAR(255) DEFAULT 'Explore <em>Without Limits</em> Your <br>Journey Begins Here.',
    hero_text_sub VARCHAR(255) DEFAULT 'Explore Without Limits',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO settings (id, hero_type, hero_url, hero_text_main, hero_text_sub)
VALUES (1, 'video', 'https://cdn.pixabay.com/video/2025/05/06/277097_large.mp4', 'Explore <em>Without Limits</em> Your <br>Journey Begins Here.', 'Explore Without Limits')
ON DUPLICATE KEY UPDATE id=id;

-- ─── PACKAGES ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    destination VARCHAR(100),
    nights INT DEFAULT 4,
    days INT DEFAULT 5,
    price DECIMAL(10,2),
    original_price DECIMAL(10,2),
    image_url VARCHAR(500),
    short_desc TEXT,
    itinerary JSON,
    is_active TINYINT(1) DEFAULT 1,
    is_featured TINYINT(1) DEFAULT 0,
    is_trending TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO packages (title, slug, destination, nights, days, price, original_price, image_url, short_desc, is_featured) VALUES
('North Sikkim Frozen Lake Tour', 'north-sikkim-frozen-lake', 'Sikkim', 5, 6, 12999, 18999, 'https://images.unsplash.com/photo-1544735745-b81216c730ca?q=80&w=800', 'Explore the pristine frozen lakes and snow-capped peaks of North Sikkim.', 1),
('Ladakh Moonland Expedition', 'ladakh-moonland-expedition', 'Ladakh', 7, 8, 24999, 34999, 'https://images.unsplash.com/photo-1596422846543-75c6fc18a593?q=80&w=800', 'A cinematic journey through the rugged landscapes of Leh Ladakh.', 1),
('Kashmir Valley Houseboat', 'kashmir-valley-houseboat', 'Kashmir', 4, 5, 15999, 22999, 'https://images.unsplash.com/photo-1595815771614-ade9d652a65d?q=80&w=800', 'Experience the ethereal beauty of Dal Lake on a traditional Kashmiri houseboat.', 1),
('Bhutan Thunder Dragon Retreat', 'bhutan-thunder-dragon', 'Bhutan', 6, 7, 34999, 49999, 'https://images.unsplash.com/photo-1506461883276-594a12b11cf3?q=80&w=800', 'Discover the last Himalayan kingdom — a land of monasteries and mountains.', 1),
('Darjeeling & Gangtok Escape', 'darjeeling-gangtok-escape', 'Sikkim & Darjeeling', 5, 6, 10999, 15999, 'https://images.unsplash.com/photo-1586348943529-beaae6c28db9?q=80&w=800', 'The perfect escape through tea gardens, sunrise vistas and colonial charm.', 1),
('Meghalaya Living Roots', 'meghalaya-living-roots', 'Meghalaya', 4, 5, 13999, 19999, 'https://images.unsplash.com/photo-1564669168781-5dc94e4d7ca4?q=80&w=800', 'Trek through living root bridges, waterfalls and the wettest place on earth.', 1);

-- ─── LEADS ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    destination VARCHAR(100),
    travel_date DATE,
    adults VARCHAR(10),
    children VARCHAR(10),
    notes TEXT,
    source VARCHAR(100) DEFAULT 'Website Hero Form',
    status ENUM('new', 'contacted', 'converted', 'lost') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
