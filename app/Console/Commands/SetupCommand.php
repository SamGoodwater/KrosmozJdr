<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;
use PDOException;

/**
 * Setup du projet : paquets apt, mises à jour, base de données, nettoyage, réinstallation.
 *
 * Utilise .env pour la BDD (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).
 *
 * @example
 * php artisan setup --install
 * php artisan setup --update
 * php artisan setup --db
 * php artisan setup --db --no-seed
 * php artisan setup --clean
 * php artisan setup --refresh
 * php artisan setup --install --db
 */
class SetupCommand extends Command
{
    protected $signature = 'setup
                            {--install : Vérifier/installer les paquets apt et les dépendances (composer, pnpm)}
                            {--update : Mettre à jour apt, pnpm et composer}
                            {--db : Base de données (MySQL/PostgreSQL : création user et base si besoin, puis migrations + seeders)}
                            {--no-seed : Avec --db, ne pas lancer les seeders}
                            {--clean : Supprimer node_modules, vendor, locks et vider la config Laravel}
                            {--refresh : Clean puis réinstaller (composer + pnpm) et clear config}';

    protected $description = 'Setup projet : install (apt + deps), update (apt/pnpm/composer), db, clean, refresh';

    /** Paquets apt requis pour le projet (Debian/Ubuntu). */
    private const APT_PACKAGES = [
        'php' => 'PHP',
        'php-cli' => 'PHP CLI',
        'php-mysql' => 'PHP extension MySQL',
        'php-mbstring' => 'PHP mbstring',
        'php-xml' => 'PHP XML',
        'php-curl' => 'PHP cURL',
        'php-zip' => 'PHP ZIP',
        'php-tokenizer' => 'PHP tokenizer',
        'default-mysql-server' => 'Serveur MySQL',
        'default-mysql-client' => 'Client MySQL',
        'nodejs' => 'Node.js',
        'npm' => 'npm (pour pnpm)',
        'git' => 'Git',
        'unzip' => 'Unzip',
        'curl' => 'cURL',
    ];

    public function handle(): int
    {
        $install = $this->option('install');
        $update = $this->option('update');
        $db = $this->option('db');
        $clean = $this->option('clean');
        $refresh = $this->option('refresh');

        if (! $install && ! $update && ! $db && ! $clean && ! $refresh) {
            $this->info('Aucune action demandée. Utilisez --install, --update, --db, --clean ou --refresh.');
            $this->line('Exemple : php artisan setup --install --db');
            return self::SUCCESS;
        }

        if ($clean) {
            $this->runClean();
        }

        if ($refresh) {
            $this->runClean();
            $this->runInstall();
        } elseif ($install) {
            $this->runInstall();
        }

        if ($update) {
            $this->runUpdate();
        }

        if ($db) {
            $code = $this->runDb();
            if ($code !== self::SUCCESS) {
                return $code;
            }
        }

        $this->info('Setup terminé.');
        return self::SUCCESS;
    }

    /** Vérifie et installe les paquets apt manquants, affiche un tableau, puis composer install et pnpm install. */
    private function runInstall(): void
    {
        if ($this->isAptAvailable()) {
            $this->installAptPackages();
        } else {
            $this->warn('apt non disponible (pas sous Debian/Ubuntu). Vérifiez manuellement les logiciels.');
        }

        $this->installComposerDeps();
        $this->installPnpmDeps();
    }

    private function isAptAvailable(): bool
    {
        return stripos(PHP_OS, 'Linux') !== false
            && file_exists('/etc/debian_version')
            && ! empty(trim((string) shell_exec('which apt 2>/dev/null')));
    }

    private function installAptPackages(): void
    {
        $this->info('Vérification des paquets apt...');
        $rows = [];
        $toInstall = [];

        foreach (self::APT_PACKAGES as $package => $description) {
            $installed = $this->isAptPackageInstalled($package);
            $rows[] = [
                $package,
                $description,
                $installed ? 'OK' : 'Manquant',
            ];
            if (! $installed) {
                $toInstall[] = $package;
            }
        }

        $this->table(['Paquet', 'Description', 'Statut'], $rows);

        if ($toInstall !== []) {
            if (! $this->option('no-interaction') && ! $this->confirm('Installer les paquets manquants avec apt ?', true)) {
                $this->warn('Installation apt annulée.');
                return;
            }
            $list = implode(' ', array_map('escapeshellarg', $toInstall));
            passthru("sudo apt update && sudo apt install -y {$list}", $code);
            if ($code !== 0) {
                $this->error('Erreur lors de l\'installation apt.');
            } else {
                $this->info('Paquets installés.');
            }
        } else {
            $this->info('Tous les paquets apt requis sont installés.');
        }
    }

    private function isAptPackageInstalled(string $package): bool
    {
        $out = shell_exec("dpkg -l {$package} 2>/dev/null | grep -E '^ii'");
        return $out !== null && trim($out) !== '';
    }

    private function installComposerDeps(): void
    {
        if (! file_exists(base_path('composer.json'))) {
            return;
        }
        if (is_dir(base_path('vendor'))) {
            $this->info('Dépendances Composer déjà présentes.');
            return;
        }
        $this->info('Installation des dépendances Composer...');
        $code = $this->runShell('composer install');
        if ($code === 0) {
            $this->info('Composer install OK.');
        } else {
            $this->warn('Vérifiez que Composer est installé (getcomposer.org).');
        }
    }

    private function installPnpmDeps(): void
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }
        if (is_dir(base_path('node_modules'))) {
            $this->info('Dépendances pnpm déjà présentes.');
            return;
        }
        $pnpm = trim((string) shell_exec('which pnpm 2>/dev/null'));
        if ($pnpm === '') {
            $this->warn('pnpm non trouvé. Installez-le (npm install -g pnpm ou corepack enable pnpm).');
            return;
        }
        $this->info('Installation des dépendances pnpm...');
        $code = $this->runShell('pnpm install');
        if ($code === 0) {
            $this->info('pnpm install OK.');
        } else {
            $this->warn('Échec pnpm install.');
        }
    }

    /** Met à jour : apt upgrade, pnpm global, composer self-update. */
    private function runUpdate(): void
    {
        if ($this->isAptAvailable()) {
            $this->info('Mise à jour apt...');
            passthru('sudo apt update && sudo apt upgrade -y', $code);
            if ($code !== 0) {
                $this->warn('Mise à jour apt non effectuée.');
            }
        }

        $this->info('Mise à jour de pnpm...');
        $this->runShell('pnpm add -g pnpm@latest');

        $this->info('Mise à jour de Composer...');
        $this->runShell('composer self-update');
    }

    /** Base de données : vérif extension, création user/base si besoin (MySQL ou PostgreSQL), puis migrate + seed (sauf --no-seed). */
    private function runDb(): int
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'mysql') {
            if (! extension_loaded('pdo_mysql')) {
                $this->error('Extension PHP pdo_mysql manquante (ex. : apt install php-mysql).');
                return self::FAILURE;
            }
            if ($this->tryAppConnection()) {
                $this->info('Connexion à la base OK.');
                return $this->runMigrationAndSeed();
            }
            if (! $this->createUserAndDatabaseWithMysql()) {
                return self::FAILURE;
            }
            DB::purge($connection);
            if (! $this->tryAppConnection()) {
                $this->error('Connexion DB_USERNAME/DB_PASSWORD échoue après création.');
                return self::FAILURE;
            }
            $this->info('Utilisateur et base créés.');
            return $this->runMigrationAndSeed();
        }

        if ($driver === 'pgsql') {
            if (! extension_loaded('pdo_pgsql')) {
                $this->error('Extension PHP pdo_pgsql manquante (ex. : apt install php-pgsql).');
                return self::FAILURE;
            }
            if ($this->tryAppConnection()) {
                $this->info('Connexion à la base OK.');
                return $this->runMigrationAndSeed();
            }
            if (! $this->createUserAndDatabaseWithPostgres()) {
                return self::FAILURE;
            }
            DB::purge($connection);
            if (! $this->tryAppConnection()) {
                $this->error('Connexion DB_USERNAME/DB_PASSWORD échoue après création.');
                return self::FAILURE;
            }
            $this->info('Utilisateur et base créés.');
            return $this->runMigrationAndSeed();
        }

        $this->warn("Connexion : {$connection} ({$driver}). Création user/base non gérée.");
        return $this->runMigrationAndSeed();
    }

    private function tryAppConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            DB::connection()->getDatabaseName();
            return true;
        } catch (PDOException) {
            return false;
        }
    }

    private function createUserAndDatabaseWithMysql(): bool
    {
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
        try {
            $pdo = new PDO($dsn, 'root', $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (PDOException $e) {
            $this->error('Connexion MySQL root/DB_PASSWORD impossible : ' . $e->getMessage());
            $this->line('Pour la création auto, le compte root doit avoir pour mot de passe la valeur de DB_PASSWORD (ou DB_PASSWORD vide si root sans mot de passe).');
            return false;
        }

        $db_quoted = '`' . str_replace('`', '``', $database) . '`';
        $pdo->exec("CREATE DATABASE IF NOT EXISTS {$db_quoted} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $this->info("Base créée ou déjà existante : {$database}");

        $user_sql = "'" . str_replace("'", "''", $username) . "'";
        $password_quoted = $pdo->quote($password);
        foreach (['%', 'localhost'] as $host_part) {
            $host_sql = "'" . str_replace("'", "''", $host_part) . "'";
            try {
                $pdo->exec("CREATE USER IF NOT EXISTS {$user_sql}@{$host_sql} IDENTIFIED BY {$password_quoted}");
                $pdo->exec("GRANT ALL PRIVILEGES ON {$db_quoted}.* TO {$user_sql}@{$host_sql}");
                $this->info("Utilisateur créé ou mis à jour : {$username}@{$host_part}");
            } catch (PDOException $e) {
                $this->warn("Création user {$username}@{$host_part} : " . $e->getMessage());
            }
        }
        $pdo->exec('FLUSH PRIVILEGES');

        return true;
    }

    private function createUserAndDatabaseWithPostgres(): bool
    {
        $host = config('database.connections.pgsql.host');
        $port = config('database.connections.pgsql.port');
        $database = config('database.connections.pgsql.database');
        $username = config('database.connections.pgsql.username');
        $password = config('database.connections.pgsql.password');

        $dsn = "pgsql:host={$host};port={$port};dbname=postgres";
        try {
            $pdo = new PDO($dsn, 'postgres', $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (PDOException $e) {
            $this->error('Connexion postgres/DB_PASSWORD impossible : ' . $e->getMessage());
            $this->line('Vérifiez que l’utilisateur postgres existe et que DB_PASSWORD correspond à son mot de passe.');
            return false;
        }

        $app_user_exists = (bool) $pdo->query("SELECT 1 FROM pg_roles WHERE rolname = " . $pdo->quote($username))->fetchColumn();
        if (! $app_user_exists) {
            $safe_password = str_replace("'", "''", $password);
            $pdo->exec("CREATE ROLE " . $this->quoteIdentifier($username) . " WITH LOGIN PASSWORD '{$safe_password}'");
            $this->info("Utilisateur créé : {$username}");
        }

        $db_exists = (bool) $pdo->query("SELECT 1 FROM pg_database WHERE datname = " . $pdo->quote($database))->fetchColumn();
        if (! $db_exists) {
            $quoted_user = $this->quoteIdentifier($username);
            $quoted_db = $this->quoteIdentifier($database);
            $pdo->exec("CREATE DATABASE {$quoted_db} OWNER {$quoted_user} ENCODING 'UTF8'");
            $this->info("Base créée : {$database}");
        }

        return true;
    }

    private function quoteIdentifier(string $name): string
    {
        return '"' . str_replace('"', '""', $name) . '"';
    }

    private function runMigrationAndSeed(): int
    {
        $this->info('Lancement des migrations...');
        $code = $this->call('migrate', ['--force' => true]);
        if ($code !== 0) {
            return $code;
        }

        if (! $this->option('no-seed')) {
            $this->info('Lancement des seeders...');
            $code = $this->call('db:seed', ['--force' => true]);
            if ($code !== 0) {
                return $code;
            }
        }

        return self::SUCCESS;
    }

    /** Supprime node_modules, pnpm-lock.yaml, vendor, composer.lock et vide config/cache Laravel. */
    private function runClean(): void
    {
        $base = base_path();

        $removals = [
            'node_modules' => is_dir($base . '/node_modules'),
            'pnpm-lock.yaml' => file_exists($base . '/pnpm-lock.yaml'),
            'vendor' => is_dir($base . '/vendor'),
            'composer.lock' => file_exists($base . '/composer.lock'),
        ];

        foreach ($removals as $name => $exists) {
            if (! $exists) {
                continue;
            }
            $path = $base . '/' . $name;
            if (is_dir($path)) {
                $this->info("Suppression de {$name}...");
                shell_exec("rm -rf " . escapeshellarg($path));
            } else {
                $this->info("Suppression de {$name}...");
                @unlink($path);
            }
        }

        $this->info('Vidage config et cache Laravel...');
        $this->call('config:clear');
        $this->call('cache:clear');
    }

    private function runShell(string $command): int
    {
        $full = "{$command} 2>&1";
        $output = [];
        exec($full, $output, $code);
        if ($output !== []) {
            $this->line(implode("\n", $output));
        }
        return $code;
    }
}
