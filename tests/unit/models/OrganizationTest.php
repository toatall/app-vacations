<?php

namespace tests\unit\models;

use app\models\Organization;
use app\fixtures\OrganizationFixture;

class OrganizationTest extends \Codeception\Test\Unit
{    
    
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => OrganizationFixture::class,
                'dataFile' => codecept_data_dir() . 'organization.php'
            ]
        ];
    }

    public function testFindActual()
    {
        $models = Organization::findActual()->indexBy('code')->asArray()->all();        
        $this->assertArrayHasKey('8601', $models);
        $this->assertArrayNotHasKey('8602', $models);
    }

    public function testValidate()
    {
        $model = new Organization();
                
        // code and name has been required
        $this->assertFalse($model->save());
        $errors = $model->getErrors();
        $this->assertArrayHasKey('code', $errors);
        $this->assertArrayHasKey('name', $errors); 
        
        // code has been unique
        $model->code = '8600';
        $model->name = 'Test organization';
        $this->assertFalse($model->save());
        $errors = $model->getErrors();
        $this->assertArrayHasKey('code', $errors);
        $this->assertArrayNotHasKey('name', $errors);

        // save full information
        $model->code = '8610';
        $model->name = 'Test organization 10';
        $this->assertTrue($model->save());        
    }

    public function testFindByCode()
    {
        $testCode = '8600';
        $model = Organization::findByCode($testCode);
        $this->assertNotEmpty($model);
        $this->assertEquals($model['code'], $testCode);
    }

    public function testFromArray()
    {
        $attr = [
            'code' => '8899',
            'name' => 'Test organization',
        ];
        $model = Organization::fromArray($attr);
        $this->assertInstanceOf(Organization::class, $model);
        $this->assertEquals($attr['code'], $model->code);
        $this->assertEquals($attr['name'], $model->name);
    }

    public function testFindByParams()
    {
        // only search
        $items = Organization::findByParams('86')->getModels();
        $this->assertCount(2, $items);

        // with trashed 
        $items = Organization::findByParams('86', 'with')->getModels();
        $this->assertCount(3, $items);

        // only trashed 
        $items = Organization::findByParams('86', 'only')->getModels();
        $this->assertCount(1, $items);
    }

    public function testGetPairs()
    {
        $items = Organization::getPairs();
        $this->assertCount(2, $items);
        $this->assertContains('8600', array_column($items, 'code'));
        $this->assertContains('8601', array_column($items, 'code'));
        $this->assertNotContains('8602', array_column($items, 'code'));
    }

}