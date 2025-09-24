<?php
/**
 * Migration Runner for SPBU Management System
 * 
 * This script runs all SQL migration files in order
 */

require_once 'php/config/config.php';

class MigrationRunner {
    private $db;
    private $migrationPath;
    
    public function __construct() {
        $this->migrationPath = __DIR__;
        $this->connect();
        $this->createMigrationTable();
    }
    
    private function connect() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->db = new PDO($dsn, DB_USER, DB_PASS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            echo "✅ Connected to MySQL database: " . DB_NAME . "\n";
            
        } catch (PDOException $e) {
            die("❌ Database connection failed: " . $e->getMessage() . "\n");
        }
    }
    
    private function createMigrationTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `migrations` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `filename` VARCHAR(255) NOT NULL UNIQUE,
            `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=MyISAM";
        
        $this->db->exec($sql);
    }
    
    public function runMigrations() {
        $migrationFiles = glob($this->migrationPath . '/*.sql');
        sort($migrationFiles); // Ensure order
        
        echo "🔍 Found " . count($migrationFiles) . " migration files\n\n";
        
        foreach ($migrationFiles as $file) {
            $filename = basename($file);
            
            // Skip if already executed
            if ($this->isMigrationExecuted($filename)) {
                echo "⏭️  Skipping {$filename} (already executed)\n";
                continue;
            }
            
            echo "🔄 Running {$filename}...\n";
            
            try {
                $sql = file_get_contents($file);
                $this->db->exec($sql);
                $this->markMigrationExecuted($filename);
                echo "✅ {$filename} executed successfully\n\n";
                
            } catch (PDOException $e) {
                echo "❌ Error executing {$filename}: " . $e->getMessage() . "\n";
                break; // Stop on error
            }
        }
        
        echo "🏁 Migration process completed!\n";
    }
    
    private function isMigrationExecuted($filename) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM migrations WHERE filename = ?");
        $stmt->execute([$filename]);
        return $stmt->fetchColumn() > 0;
    }
    
    private function markMigrationExecuted($filename) {
        $stmt = $this->db->prepare("INSERT INTO migrations (filename) VALUES (?)");
        $stmt->execute([$filename]);
    }
    
    public function rollback($steps = 1) {
        echo "🔄 Rolling back {$steps} migration(s)...\n";
        
        $stmt = $this->db->prepare("
            SELECT filename FROM migrations 
            ORDER BY executed_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$steps]);
        $migrations = $stmt->fetchAll();
        
        foreach ($migrations as $migration) {
            echo "⏪ Rolling back {$migration['filename']}...\n";
            // Note: This is a simple example. In practice, you'd need down migrations
            $this->db->prepare("DELETE FROM migrations WHERE filename = ?")
                     ->execute([$migration['filename']]);
            echo "✅ {$migration['filename']} rolled back\n";
        }
        
        echo "🏁 Rollback completed!\n";
    }
    
    public function status() {
        echo "📊 Migration Status:\n\n";
        
        $stmt = $this->db->query("SELECT * FROM migrations ORDER BY executed_at");
        $executed = $stmt->fetchAll();
        
        $migrationFiles = glob($this->migrationPath . '/*.sql');
        sort($migrationFiles);
        
        foreach ($migrationFiles as $file) {
            $filename = basename($file);
            $isExecuted = false;
            
            foreach ($executed as $exec) {
                if ($exec['filename'] === $filename) {
                    echo "✅ {$filename} (executed at {$exec['executed_at']})\n";
                    $isExecuted = true;
                    break;
                }
            }
            
            if (!$isExecuted) {
                echo "⏳ {$filename} (pending)\n";
            }
        }
        
        echo "\n📈 Total: " . count($migrationFiles) . " migrations, " . count($executed) . " executed\n";
    }
}

// CLI Interface
if (php_sapi_name() === 'cli') {
    $runner = new MigrationRunner();
    
    $command = $argv[1] ?? 'migrate';
    
    switch ($command) {
        case 'migrate':
            $runner->runMigrations();
            break;
            
        case 'rollback':
            $steps = intval($argv[2] ?? 1);
            $runner->rollback($steps);
            break;
            
        case 'status':
            $runner->status();
            break;
            
        default:
            echo "Usage: php migration_runner.php [migrate|rollback|status]\n";
            echo "  migrate  - Run pending migrations\n";
            echo "  rollback [steps] - Rollback migrations (default: 1)\n";
            echo "  status   - Show migration status\n";
            break;
    }
} else {
    echo "This script must be run from command line\n";
}
?>