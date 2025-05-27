php yii migrate/down --interactive=0 9
php yii migrate/down --migrationPath=@yii/rbac/migrations --interactive=0 4
php yii migrate --migrationPath=@yii/rbac/migrations --interactive=0
php yii migrate --interactive=0

php yii db/seed
php yii roles/init
php yii roles/assign "admin" "admin"

set "currentYear=%date:~-4%"

php yii vacations/generate 100 8 runtime\vacations_0000.csv
php yii csv-loader runtime\vacations_0001.csv 0000 %currentYear% ";"
php yii vacations/generate 80 8 runtime\vacations_0001.csv
php yii csv-loader runtime\vacations_0001.csv 0001 %currentYear% ";"
php yii vacations/generate 70 8 runtime\vacations_0002.csv
php yii csv-loader runtime\vacations_0002.csv 0002 %currentYear% ";"
php yii vacations/generate 90 10 runtime\vacations_0003.csv
php yii csv-loader runtime\vacations_0003.csv 0003 %currentYear% ";"