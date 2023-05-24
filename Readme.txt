Step.1 
#install Global Laravel installer
composer global require laravel/installer

Step 2.
laravel new blog
cd blog

Step 3.
#https://github.com/barryvdh/laravel-ide-helper

composer require --dev barryvdh/laravel-ide-helper

#Insert line ->     config/app.php
Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,

php artisan clear-compiled

php artisan ide-helper:generate

Step 4.
#Automatic phpDocs for models
php artisan ide-helper:models Post
php artisan ide-helper:models -M

Step 5.
#PhpStorm Meta for Container instances
php artisan ide-helper:meta


Step 6.
#Command Line Tools | artisan
#https://blog.jetbrains.com/phpstorm/2013/09/command-line-tools-based-on-symfony-console-doctrine-laravel-in-phpstorm/
Settings -> Tools -> Command Line Tool Support
Add -> Select Type: Tool base symfony Console

Step 7.
Settings -> Languages & Frameworks -> PHP -> Laravel
Check -> Enable Plugin
Check -> Use AutoPopup

Step 8.
php artisan serve

#Important
File Open-> composer.json -> Find -> "autoload"
,
"files": [
            "app/Helpers/Functions.php"
        ]

#To force the autoload file to regenerate just run the command below
composer dumpautoload
# or
composer dump-autoload

#Update composer Dependencies
composer update
