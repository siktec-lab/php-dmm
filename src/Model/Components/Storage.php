<?php

namespace Siktec\Dmm\Model\Components;

use \Siktec\Dmm\Exceptions;
use \Siktec\Dmm\Model\Traits;
use \Siktec\Dmm\Model\Attr;

/**
 * Class Storage
 *
 * @package Siktec\Dmm\Model\Components
 */
class Storage
{
    use Traits\ClassAttributesParserTrait;

    public const ATTR_CONNECTION_NAME = "name";
    public const ATTR_CONNECTION_KEY  = "key";

    private ?object $ref = null;
    private string $connection = "default";
    private ?string $key = null;

    /**
     * @param object|null $ref the class to parse
     * @param bool $parse whether to parse the class attributes
     * @param bool $throw whether to throw if the class attributes are invalid
     */
    public function __construct(?object $ref = null, bool $parse = true, bool $throw = true)
    {
        $this->ref = $ref;
        if ($parse) {
            $this->parse($throw);
        }
    }

    /**
     * Parses the class attributes
     * @param bool $throw whether to throw if the class attributes are invalid
     *
     * @return bool successfull parse
     */
    public function parse(bool $throw = true): bool
    {
        $connection_meta = $this->extractClassMeta(Attr\Connection::class, $this->ref);

        if (!array_key_exists(self::ATTR_CONNECTION_NAME, $connection_meta)) {
            if (!$throw) {
                return false;
            }
            throw new Exceptions\ModelDeclarationException(
                [get_class($this->ref), Attr\Connection::class],
                151
            );
        }

        $this->connection = $connection_meta[self::ATTR_CONNECTION_NAME];
        $this->key = $this->keyNormalize(
            $connection_meta[self::ATTR_CONNECTION_KEY] ?? get_class($this->ref)
        );

        //If the key or connection name is empty throw
        if (!$this->connection || !$this->key) {
            if (!$throw) {
                return false;
            }
            throw new Exceptions\ModelDeclarationException(
                [get_class($this->ref), Attr\Connection::class, "name, key"],
                155
            );
        }
        return true;
    }

    /**
     * Normalizes the key
     * only keeps the last part of the namespace and removes any non alphanumeric characters
     *
     * @param string $key
     *
     * @return string
     */
    private function keyNormalize(string $key): string
    {
        $key = explode("\\", strtolower($key));
        return preg_replace(
            "/[^a-z0-9_]/",
            "",
            array_pop($key)
        );
    }

    /**
     * Returns the connection name
     * If a name is provided it will check if it matches the connection name
     *
     * @param string|null $name the name of the connection to check
     *
     * @return string|bool the connection name or true if the name matches the connection name
     */
    public function connection(?string $name = null): string|bool
    {
        return $name ? $name === $this->connection : $this->connection;
    }

    /**
     * Returns the key name
     *
     * @return string|null
     */
    public function key(): ?string
    {
        return $this->key;
    }

    /**
     * Returns the connection and key as a string in the format connection:key
     *
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->connection}:{$this->key}";
    }
}
