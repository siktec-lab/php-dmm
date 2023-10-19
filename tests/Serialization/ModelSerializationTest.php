<?php

declare(strict_types=1);

namespace Siktec\Dmm\Tests\Serialization;

use \PHPUnit\Framework\TestCase;
use \Siktec\Dmm\Tests\Resources;

final class ModelSerializationTest extends TestCase
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

    public function testSimpleModelToArray(): void
    {
        $user = new Resources\Models\ComplexUserFriends(
            name: "bob",
            numerical_age: 15,
            email_address: "example@mail.com"
        );

        // Init from constructor without any validation:
        $this->assertTrue($user->isValid());

        $data_ex = $user->toArray(external: true, generated: true, nested: true);
        $data_in = $user->toArray(external: false, generated: true, nested: true);
        $data_wo_gen = $user->toArray(external: true, generated: false, nested: true);

        $this->assertSame(
            [
                "name"      => "bob", 
                "age"       => 15, 
                "email"     => "example@mail.com",
                "too_young" => true,
                "friend"    => null,
                "names"     => [],
                "message"   => "Hello bob"
            ],
            $data_ex
        );

        $this->assertSame(
            [
                "name"          => "bob", 
                "numerical_age" => 15, 
                "email_address" => "example@mail.com",
                "too_young"     => true,
                "friend"        => null,
                "names"         => [],
                "message"       => "Hello bob"
            ],
            $data_in
        );

        $this->assertSame(
            [
                "name"      => "bob", 
                "age"       => 15, 
                "email"     => "example@mail.com",
                "friend"    => null,
                "names"     => []
            ],
            $data_wo_gen
        );

    }

    public function testJsonSerialization(): void
    {
        $user = new Resources\Models\SimpleUser();

        $valid = $user->fromArray([
            "name"  => "bob",
            "age"   => 15,
            "email" => "example@mail.com"
        ]);

        $this->assertTrue($valid);

        $this->assertSame(
            '{"name":"bob","age":15,"email":"example@mail.com","greet":"Hello bob"}',
            json_encode($user)
        );
    }
        
}
