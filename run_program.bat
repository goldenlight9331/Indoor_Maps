@echo off
cd IndoorBackEnd
start cmd /k "php artisan serve"
cd ..

cd indoor-navigation
start cmd /k "npm run dev"
cd ..

exit