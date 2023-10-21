<?php

declare(strict_types=1);

namespace Siktec\Dmm\Tests\Declaration;

use \PHPUnit\Framework\TestCase;
use \Siktec\Dmm\Tests\Resources;
use \Siktec\Dmm\Exceptions;

final class ModelIndexesTest extends TestCase
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

    public function testIndexesExists(): void
    {
        $user = new Resources\Models\SimpleUser();

        $this->assertSame(
            "primary",
            $user->_properties->isIndex('name')
        );

        $this->assertFalse(
            $user->_properties->isIndex('age')
        );

        $this->assertSame(
            ["name" => "primary"],
            $user->_properties->getIndexes()
        );
    }

    public function testNoPrimaryException(): void
    {
        // Should throw an exception because there are no properties:
        $this->expectException(Exceptions\ModelDeclarationException::class);

        $user = new Resources\Models\NoPrimary();   
    }

    public function testMultiplePrimaryException(): void
    {
        // Should throw an exception because there are no properties:
        $this->expectException(Exceptions\ModelDeclarationException::class);

        $user = new Resources\Models\MultiplePrimary();   
    }

}
