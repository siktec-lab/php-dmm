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
            email: "example@mail.com"
        );

        // Init from constructor without any validation:
        $this->assertTrue($user->isValid());

        // Init from constructor:
        $this->assertSame("bob", $user->name);
        $this->assertSame(15, $user->age);
        $this->assertSame("example@mail.com", $user->email);
        $this->assertSame("Hello bob", $user->greet);
    }

    public function testSimpleArrayInitialization(): void
    {
        $user = new Resources\Models\SimpleUser();

        $valid = $user->fromArray([
            "name"  => "bob",
            "age"   => 15,
            "email" => "example@mail.com"
        ]);

        $this->assertTrue($valid);
        $this->assertSame("bob", $user->name);
        $this->assertSame(15, $user->age);
        $this->assertSame("example@mail.com", $user->email);
        $this->assertSame("Hello bob", $user->greet);
    }

    public function testSimpleJsonInitialization(): void
    {
        $user = new Resources\Models\SimpleUser();

        $valid = $user->fromJson('{"name":"bob","age":15,"email":"example@mail.com"}');

        $this->assertTrue($valid);
        $this->assertSame("bob", $user->name);
        $this->assertSame(15, $user->age);
        $this->assertSame("example@mail.com", $user->email);
        $this->assertSame("Hello bob", $user->greet);
    }

    public function testNestedModelInitialization(): void
    {
        $user = new Resources\Models\ComplexUserFriends();

        $valid = $user->fromArray([
            "name"          => "shane",
            "numerical_age" => 25,
            "email_address" => "example@mail.com",
            "should_be"     => "ignored", // Should be ignored because it's not a property
            "friend" => [
                "name"          => "bob",
                "numerical_age" => 20,
                "email_address" => "example@mail.com",
                "friend" => [
                    "name"          => "john",
                    "numerical_age" => 60,
                    "email_address" => "example@mail.com"
                ]
            ]
        ], false);

        $this->assertTrue($valid);
        $this->assertSame("Hello shane", $user->message);
        $this->assertSame("Hello bob", $user->friend->message);
        $this->assertSame("Hello john", $user->friend->friend->message);

    }
        
}
