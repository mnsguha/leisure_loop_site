import sys

file_path = "config/db.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

new_migration = """
    // Auto-migrate vehicle rates
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS vehicle_rates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            vehicle_id INT NOT NULL,
            rate_date DATE NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_vehicle_date (vehicle_id, rate_date),
            FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    } catch (PDOException $e) {
        // Safe failover
    }
"""

content = content.replace("} catch (PDOException $e) {\n    $pdo = null;", new_migration + "\n} catch (PDOException $e) {\n    $pdo = null;")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Migration injected into db.php")
