#!/usr/bin/env bash
set -euo pipefail

APP_ROOT="/var/www/html"
cd "$APP_ROOT"

# ---------------------------------------------------------------------------
# Default environment (dapat dioverride lewat docker-compose / .env.docker)
# ---------------------------------------------------------------------------
export CI_ENVIRONMENT="${CI_ENVIRONMENT:-production}"
export APP_BASE_URL="${APP_BASE_URL:-http://localhost:8080/}"

export DB_HOST="${DB_HOST:-host.docker.internal}"
export DB_PORT="${DB_PORT:-3306}"
export DB_DATABASE="${DB_DATABASE:-streaming_db}"
export DB_USERNAME="${DB_USERNAME:-streamer}"
export DB_PASSWORD="${DB_PASSWORD:-}"
export DB_DRIVER="${DB_DRIVER:-MySQLi}"

export HLS_ENABLED="${HLS_ENABLED:-true}"
export HLS_FFMPEG_PATH="${HLS_FFMPEG_PATH:-/usr/bin/ffmpeg}"
export ENCRYPTION_KEY="${ENCRYPTION_KEY:-}"

# ---------------------------------------------------------------------------
# Folder writable (volume bisa kosong saat pertama kali dijalankan)
# ---------------------------------------------------------------------------
mkdir -p \
    writable/cache \
    writable/logs \
    writable/session \
    writable/uploads \
    writable/hls

if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data writable
fi

# ---------------------------------------------------------------------------
# Tunggu database eksternal
# ---------------------------------------------------------------------------
if [ "${WAIT_FOR_DB:-true}" = "true" ]; then
    echo "Menunggu database ${DB_HOST}:${DB_PORT} ..."
    php -r '
        $host = getenv("DB_HOST") ?: "host.docker.internal";
        $port = getenv("DB_PORT") ?: "3306";
        $user = getenv("DB_USERNAME") ?: "streamer";
        $pass = getenv("DB_PASSWORD") ?: "";
        $db   = getenv("DB_DATABASE") ?: "streaming_db";

        for ($i = 1; $i <= 30; $i++) {
            try {
                new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass, [
                    PDO::ATTR_TIMEOUT => 3,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
                fwrite(STDOUT, "Database terhubung.\n");
                exit(0);
            } catch (Throwable $e) {
                fwrite(STDOUT, "  percobaan {$i}/30 gagal, menunggu 2 detik...\n");
                sleep(2);
            }
        }

        fwrite(STDERR, "PERINGATAN: database belum bisa dihubungi. Container tetap dijalankan.\n");
        exit(0);
    '
fi

# ---------------------------------------------------------------------------
# Generate .env CodeIgniter dari environment container
# ---------------------------------------------------------------------------
if [ "${GENERATE_ENV:-true}" = "true" ]; then
    php -r '
        $values = [
            "CI_ENVIRONMENT"               => getenv("CI_ENVIRONMENT") ?: "production",
            "app.baseURL"                  => getenv("APP_BASE_URL") ?: "http://localhost:8080/",
            "database.default.hostname"    => getenv("DB_HOST") ?: "host.docker.internal",
            "database.default.port"        => getenv("DB_PORT") ?: "3306",
            "database.default.database"    => getenv("DB_DATABASE") ?: "streaming_db",
            "database.default.username"    => getenv("DB_USERNAME") ?: "streamer",
            "database.default.password"    => getenv("DB_PASSWORD") ?: "",
            "database.default.DBDriver"    => getenv("DB_DRIVER") ?: "MySQLi",
            "hls.enabled"                  => getenv("HLS_ENABLED") ?: "true",
            "hls.ffmpegPath"               => getenv("HLS_FFMPEG_PATH") ?: "/usr/bin/ffmpeg",
        ];

        $key = getenv("ENCRYPTION_KEY") ?: "";
        if ($key !== "") {
            $values["encryption.key"] = $key;
        }

        $lines = ["# Dibuat otomatis oleh docker/entrypoint.sh"];
        foreach ($values as $name => $value) {
            $escaped = str_replace(["\\", "\x27"], ["\\\\", "\\\x27"], (string) $value);
            $lines[] = $name . " = \x27" . $escaped . "\x27";
        }

        file_put_contents("/var/www/html/.env", implode("\n", $lines) . "\n");
    '
    if [ "$(id -u)" = "0" ]; then
        chown www-data:www-data "$APP_ROOT/.env"
    fi
fi

# ---------------------------------------------------------------------------
# Migrasi opsional (default: false)
# ---------------------------------------------------------------------------
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Menjalankan migrasi database ..."
    php spark migrate --all || echo "PERINGATAN: migrasi gagal, periksa koneksi database."
fi

# ---------------------------------------------------------------------------
# Pastikan writable tetap bisa ditulis (volume bisa dimount ulang)
# ---------------------------------------------------------------------------
if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data writable
fi

echo "Media Streamer siap: ${APP_BASE_URL} (environment: ${CI_ENVIRONMENT})"

exec "$@"
