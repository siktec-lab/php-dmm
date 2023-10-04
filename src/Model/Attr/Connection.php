<?php 

namespace Siktec\PhpRedis\Model\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Connection {
    /**
     * @param string $name the name of the connection to use
     * @param string|null $key the name of the key to use, if null the class name will be used
     */
    public function __construct(
        public string  $name = 'default', 
        public ?string $key = null
    ) {
    }
}