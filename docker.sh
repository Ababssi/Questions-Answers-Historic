sleep 3
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
php bin/console d:m:m --no-interaction
php bin/console messenger:consume async -vv &
exec apache2-foreground