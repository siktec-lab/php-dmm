<?php

declare(strict_types=1);

namespace Siktec\Dmm\Tests\Initialization;

use \PHPUnit\Framework\TestCase;
use \Siktec\Dmm\Tests\Resources;

final class ModelInitializationTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        return;
    }

    public static function tearDownAfterClass(): void
    {
        return;
    }

    public function setUp(): void
    {
        return;
    }

    public function tearDown(): void
    {
        return;
    }

    public function testSimpleConstructorInitialization(): void
    {
        $user = new Resources\Models\SimpleUser(
            name: "bob",
            age: 15,
            email: "example@bob.com"
        );

        // Init from constructor without any validation:
        $this->assertTrue($user->isValid());

        // Init from constructor:
        $this->assertSame("bob", $user->name);
        $this->assertSame(15, $user->age);
        $this->assertSame("example@bob.com", $user->email);
        $this->assertSame("Hello bob", $user->greet);
    }
}
