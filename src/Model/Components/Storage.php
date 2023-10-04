<?php

namespace Siktec\PhpRedis\Model\Components;

use \Siktec\PhpRedis\Exceptions;
use \Siktec\PhpRedis\Model\Traits;
use \Siktec\PhpRedis\Model\Attr;

class Storage {

    use Traits\ClassAttributesParserTrait;

    public const ATTR_CONNECTION_NAME = "name";
    public const ATTR_CONNECTION_KEY  = "key";

    private ?object $ref     = null;
    private string $connection = "default";
    private ?string $key       = null;

    public function __construct(?object $ref = null, bool $parse = true, bool $throw = true)
    {
        $this->ref = $ref;
        if ($parse) {
            $this->parse($throw);
        }
    }

    public function parse(bool $throw = true) : bool
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
        $this->key = strtolower($meta[self::ATTR_CONNECTION_KEY] ?? get_class($this->ref));

        return true;
    }

    public function connection(?string $name = null) : string
    {
        return $name ? $name === $this->connection : $this->connection;
    }

    public function key() : string
    {
        return $this->key;
    }

    public function __toString() : string
    {
        return "{$this->connection}:{$this->key}";
    }
}
    