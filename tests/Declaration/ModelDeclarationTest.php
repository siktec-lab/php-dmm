<?php declare(strict_types=1);

namespace Siktec\Dmm\Tests\Declaration;

use \PHPUnit\Framework\TestCase;
use \Siktec\Dmm\Tests\Resources;

final class ModelDeclarationTest extends TestCase
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

    public function testSimpleDeclaration() : void {

        $user = new Resources\Models\SimpleUser();

        // Loaded from constructor without any validation:
        $this->assertFalse($user->isLoaded());

    }

}