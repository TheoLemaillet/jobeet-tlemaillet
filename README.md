TotoJob-Tlemaillet
==================
__A Symfony project created on May 19.__

__Manipulation a effectuer pour installation:__  
	git clone https://github.com/TheoLemaillet/jobeet-tlemaillet.git  
	cd jobeet-tlemaillet  
	composer install  
	php app/console doctrine:database:create  
	php app/console doctrine:fixtures:load  
	php app/console assets:install web  
	php app/console ens:toto:users admin admin  
	php app/console cache:clear --env=prod  
	php app/console cache:clear --env=dev  
	php app/console server:run  

__Effectuer les tests unitaires:__  
	./phpunit-5.3.4.phar -c app/

__Tester le fonctionnement du site:__  
Decommenter les switchs et commenter les originaux aux lignes 78 et 97 du fichier:  
	./src/Ens/TotoBundle/DataFixtures/ORM/LoadJobData.php  
Puis:  
	php app/console doctrine:fixtures:load  
	php app/console cache:clear --env=prod  
	php app/console cache:clear --env=dev  

__List des bugs connus:__  
https://github.com/TheoLemaillet/jobeet-tlemaillet/issues
