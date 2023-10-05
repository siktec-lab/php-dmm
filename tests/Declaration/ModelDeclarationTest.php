<?php

declare(strict_types=1);

namespace Siktec\Dmm\Tests\Declaration;

use \PHPUnit\Framework\TestCase;
use \Siktec\Dmm\Tests\Resources;
use \Siktec\Dmm\Exceptions;

final class ModelDeclarationTest extends TestCase
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

    public function testSimpleDeclaration(): void
    {
        $user = new Resources\Models\SimpleUser();

        // Loaded from constructor without any validation:
        $this->assertFalse($user->isLoaded());
    }

    public function testNoPropertiesDeclaration(): void
    {
        // Should throw an exception because there are no properties:
        $this->expectException(Exceptions\ModelDeclarationException::class);

        $user = new Resources\Models\NoPropertiesModel();
    }

    public function testMissingConnectionAttribute(): void
    {
        // Should throw an exception because there are no properties:
        $this->expectException(Exceptions\ModelDeclarationException::class);

        $user = new Resources\Models\MissingConnectionModel();
    }

    public function testConnectionAttributeInvalidNameOrKey(): void
    {
        // Should throw an exception because the connection attribute has an invalid name:
        $this->expectException(Exceptions\ModelDeclarationException::class);

        $user = new Resources\Models\InvalidConnectionValuesModel();
    }

    public function testMultipleTypesOfSameProperty(): void
    {
        // Should throw an exception because the connection attribute has an invalid name:
        $this->expectException(Exceptions\ModelDeclarationException::class);

        $user = new Resources\Models\MultipleTypesOfSamePropertyModel();
    }
}
