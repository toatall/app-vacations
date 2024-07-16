<?php

namespace app\commands;

use app\models\User;
use Yii;
use yii\console\Controller;
use Faker\Factory;

class DbController extends Controller
{

    protected function getDb()
    {
        return Yii::$app->db;
    }

    public function actionSeed()
    {
        $db = $this->getDb();
        $faker = Factory::create();
        $usaStates = $this->usaStates();

        $db->createCommand()
            ->insert('accounts', [
                'id' => 1,
                'name' => 'Acme Corporation',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ])->execute();
        $idAccount = $db->getLastInsertID('accounts');

        for($i=0; $i<100; $i++) {
            $db->createCommand()
                ->insert('organizations', [
                    'id' => null, // automatic pk
                    'account_id' => $idAccount,
                    'name' => $faker->company,
                    'email' => $faker->safeEmail,
                    'phone' => $faker->phoneNumber,
                    'address' => $faker->streetAddress,
                    'city' => $faker->city,
                    'region' => $faker->randomElement($usaStates),
                    'country' => 'US',
                    'postal_code' => $faker->postcode,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ])->execute();

            $idOrganization = $db->getLastInsertID('organization');
            
            $db->createCommand()
                ->insert('contacts', [
                    'id' => null, // automatic pk
                    'account_id' => $idAccount,
                    'organization_id' => $idOrganization,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $faker->unique()->safeEmail,
                    'phone' => $faker->phoneNumber,
                    'address' => $faker->streetAddress,
                    'city' => $faker->city,
                    'region' => $faker->randomElement($usaStates),
                    'country' => 'US',
                    'postal_code' => $faker->postcode,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ])->execute();
        }
        
        for($i=0; $i<100; $i++) {
            $db->createCommand()
                ->insert('users', [
                    'id' => null, // automatic pk
                    'account_id' => $idAccount,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $faker->unique()->safeEmail,
                    'password' => 'secret',
                    'owner' => '0',
                    'remember_token' => $faker->regexify('[A-Za-z0-9]{10}'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ])->execute();
        }            

        $user = new User();
        $user->detachBehaviors();
        $user->attributes = [
            'account_id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => 'secret',
            'owner' => 1
        ];
        $user->save(false);
    }

    private function usaStates()
    {
        return [
            'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Canal Zone', 'Colorado', 'Connecticut', 'Delaware', 'District of Columbia', 
            'Florida', 'Georgia', 'Guam', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 
            'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 
            'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Puerto Rico', 'Rhode Island', 
            'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virgin Islands', 'Virginia', 'Washington', 'West Virginia', 
            'Wisconsin', 'Wyoming',
        ];
    }

}
