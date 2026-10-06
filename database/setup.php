<?php
declare(strict_types=1);

/**
 * ELMS Database Setup & Migration Script
 * Usage:
 *   php database/setup.php          (MySQL Setup - Default)
 *   php database/setup.php --sqlite (SQLite Setup - Zero-Config Instant Mode)
 */

require_once __DIR__ . '/../config/config.php';

$isSqlite = in_array('--sqlite', $argv ?? []) || ($_ENV['DB_CONNECTION'] ?? '') === 'sqlite';

echo "========================================================\n";
echo " ELMS - Database Setup & Seeder Engine\n";
echo " Mode: " . ($isSqlite ? "SQLite (Zero-Config Portable)" : "MySQL (Enterprise Server)") . "\n";
echo "========================================================\n";

if ($isSqlite) {
    try {
        $dbPath = __DIR__ . '/elms.sqlite';
        echo "[1/3] Preparing SQLite database at: {$dbPath}...\n";
        if (file_exists($dbPath)) {
            unlink($dbPath);
        }
        $pdo = new PDO("sqlite:{$dbPath}", null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON;');
        echo "      Database file initialized.\n\n";

        echo "[2/3] Executing schema_sqlite.sql definitions...\n";
        $schemaSql = file_get_contents(__DIR__ . '/schema_sqlite.sql');
        $pdo->exec($schemaSql);
        echo "      All 9 tables, constraints, and relationships created!\n\n";

        echo "[3/3] Executing seed_sqlite.sql demo data...\n";
        $seedSql = file_get_contents(__DIR__ . '/seed_sqlite.sql');
        $pdo->exec($seedSql);
        echo "      Seeding complete!\n";
        echo "      - 1 Admin account (admin@elms.test / Admin@123)\n";
        echo "      - 10 Employee accounts (employee1 to 10 with Employee@123)\n";
        echo "      - 5 Departments, 5 Leave Types, Leave Balances, Attendance & Logs\n\n";

        // Update .env to use sqlite
        $envFile = APP_ROOT . '/.env';
        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            if (!str_contains($envContent, 'DB_CONNECTION=')) {
                $envContent = "DB_CONNECTION=sqlite\n" . $envContent;
            } else {
                $envContent = preg_replace('/DB_CONNECTION=.*$/m', 'DB_CONNECTION=sqlite', $envContent);
            }
            file_put_contents($envFile, $envContent);
            echo "      [Config] Updated .env to DB_CONNECTION=sqlite\n\n";
        }

        echo "========================================================\n";
        echo " SUCCESS: ELMS SQLite database ready for instant testing!\n";
        echo " Run server: php -S localhost:8000 -t public\n";
        echo "========================================================\n";
        exit(0);
    } catch (Throwable $e) {
        echo "\n[ERROR] SQLite Setup Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// -------------------------------------------------------------
// MySQL Setup (Default)
// -------------------------------------------------------------
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$dbname = $_ENV['DB_NAME'] ?? 'elms_db';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';

try {
    echo "[1/4] Connecting to MySQL server at {$host}:{$port}...\n";
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
    ]);
    echo "      Connected successfully!\n\n";

    echo "[2/4] Ensuring database `{$dbname}` exists...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbname}`");
    echo "      Database `{$dbname}` ready.\n\n";

    echo "[3/4] Executing schema.sql definitions...\n";
    $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schemaSql);
    echo "      All 9 tables, constraints, and indexes created successfully!\n\n";

    echo "[4/4] Executing seed.sql demo data...\n";
    $seedSql = file_get_contents(__DIR__ . '/seed.sql');
    $pdo->exec($seedSql);
    echo "      Seeding complete!\n";
    echo "      - 1 Admin account (admin@elms.test / Admin@123)\n";
    echo "      - 10 Employee accounts (employee1 to 10 with Employee@123)\n";
    echo "      - 5 Departments, 5 Leave Types, Leave Balances, Attendance & Logs\n\n";

    echo "========================================================\n";
    echo " SUCCESS: ELMS MySQL Database is fully initialized and seeded!\n";
    echo " Run server: php -S localhost:8000 -t public\n";
    echo "========================================================\n";

} catch (PDOException $e) {
    echo "\n[ERROR] MySQL Connection Error: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting Tips:\n";
    echo " 1. If using XAMPP/WAMP, make sure the MySQL service is started.\n";
    echo " 2. Check DB_HOST, DB_USER, DB_PASSWORD in your .env file.\n";
    echo " 3. Want instant zero-config testing without waiting for MySQL?\n";
    echo "    Run: php database/setup.php --sqlite\n";
    exit(1);
} catch (Throwable $e) {
    echo "\n[ERROR] General Exception: " . $e->getMessage() . "\n";
    exit(1);
}
