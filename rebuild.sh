docker-compose down --remove-orphans;
docker-compose build --no-cache ;
docker-compose up -d ;
docker exec -it evaluate_app_php composer install ;
docker exec -it evaluate_app_php php bin/console doctrine:database:create --if-not-exists;
docker exec -it evaluate_app_php php bin/console doctrine:migrations:migrate --allow-no-migration --no-interaction;