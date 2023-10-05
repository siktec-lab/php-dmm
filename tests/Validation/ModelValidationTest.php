<?php declare(strict_types=1);

namespace Siktec\Dmm\Tests\Validation;

use \PHPUnit\Framework\TestCase;
use \Siktec\Dmm\Tests\Resources;

final class ModelValidationTest extends TestCase
{
    
    public static function setUpBeforeClass() : void {
        return;
    }

    public static function tearDownAfterClass() : void {
        return;
    }

    public function setUp() : void {
        return;
    }

    public function tearDown() : void {
        return;
    }

    public function testSimpleModelValidation() : void {

        $user = new Resources\Models\SimpleUser(
            name: "b",
            age: null,
            email: "example@bob"
        );

        // Loaded from constructor without any validation:
        $this->assertFalse($user->isLoaded());

        $validation = $user->validation();

        // Property validation:
        $this->assertArrayHasKey("name", $validation);
        $this->assertArrayHasKey("age", $validation);
        $this->assertArrayHasKey("email", $validation);
    }

}