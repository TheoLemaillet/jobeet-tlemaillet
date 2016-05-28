TotoJob-Tlemaillet
=================

git clone https://github.com/TheoLemaillet/jobeet-tlemaillet.git
cd jobeet-tlemaillet
composer install
php app/console doctrine:database:create
php app/console doctrine:fixtures:load
php app/console assets:install web
php app/console ens:toto:users admin admin
php app/console server:run

A Symfony project created on May 19.
