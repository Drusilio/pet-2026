#!/bin/bash
set -euo pipefail

wait_for_mysql() {
    if [ -z "${DATABASE_URL:-}" ]; then
        return 0
    fi

    local host="${MYSQL_HOST:-mysql}"
    local user="${MYSQL_USER:-app}"
    local password="${MYSQL_PASSWORD:-app}"

    echo "Waiting for MySQL at ${host}..."
    for _ in $(seq 1 60); do
        if mysqladmin ping -h "${host}" -u"${user}" -p"${password}" --silent; then
            echo "MySQL is ready."
            return 0
        fi
        sleep 2
    done

    echo "MySQL did not become ready in time." >&2
    return 1
}

if [ "${SKIP_MYSQL_WAIT:-0}" != "1" ]; then
    wait_for_mysql || true
fi

git config --global --add safe.directory /var/www/html >/dev/null 2>&1 || true

exec docker-php-entrypoint "$@"
