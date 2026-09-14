<?php
require_once __DIR__ . '/../config/db.php';

class Migration_20260912_add_gst_type_to_hotels {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function up() {
        try {
            // Check if column exists
            $stmt = $this->pdo->query("SHOW COLUMNS FROM `hotels` LIKE 'gst_type'");
            if (!$stmt->fetch()) {
                $this->pdo->exec("ALTER TABLE `hotels` ADD COLUMN `gst_type` ENUM('net', 'gst_inc') DEFAULT 'net' AFTER `google_review_link`");
                echo "Migration UP successful: Added gst_type to hotels.\n";
            } else {
                echo "Migration UP: Column gst_type already exists.\n";
            }
        } catch (PDOException $e) {
            echo "Migration UP failed: " . $e->getMessage() . "\n";
        }
    }

    public function down() {
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM `hotels` LIKE 'gst_type'");
            if ($stmt->fetch()) {
                $this->pdo->exec("ALTER TABLE `hotels` DROP COLUMN `gst_type`");
                echo "Migration DOWN successful: Removed gst_type from hotels.\n";
            } else {
                echo "Migration DOWN: Column gst_type does not exist.\n";
            }
        } catch (PDOException $e) {
            echo "Migration DOWN failed: " . $e->getMessage() . "\n";
        }
    }
}

$migration = new Migration_20260912_add_gst_type_to_hotels($pdo);
$migration->up();
