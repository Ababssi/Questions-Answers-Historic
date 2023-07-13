# Attente pour la base de données pour être prête
until php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1; do
    echo "En attente pour la base de données..."
    sleep 1
done
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
php bin/console d:m:m --no-interaction
php bin/console messenger:consume async -vv &
exec apache2-foreground