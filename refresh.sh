while !</dev/tcp/postgres/5432; do sleep 1; done;

php yii migrate/down --interactive=0 9;
php yii migrate/down --migrationPath=@yii/rbac/migrations --interactive=0 4;
php yii migrate --migrationPath=@yii/rbac/migrations --interactive=0;
php yii migrate --interactive=0;

php yii db/seed;
php yii roles/init;
php yii roles/assign "admin" "admin";

php yii vacations/generate 100 8 runtime/vacations.csv;
php yii csv-loader runtime/vacations.csv 0000 $(date +%Y) ";";
php yii vacations/generate 80 8 runtime/vacations.csv;
php yii csv-loader runtime/vacations.csv 0001 $(date +%Y) ";";
php yii vacations/generate 70 8 runtime/vacations.csv;
php yii csv-loader runtime/vacations.csv 0002 $(date +%Y) ";";
php yii vacations/generate 90 10 runtime/vacations.csv;
php yii csv-loader runtime/vacations.csv 0003 $(date +%Y) ";";
rm runtime/vacations.csv;

apache2-foreground