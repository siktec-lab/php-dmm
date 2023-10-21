<?php

namespace Siktec\Dmm\Model\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Property
{
    /**
     * @param string $name  the name of the property (defaults to the property name)
     * @param string $index the index type of the property (none, primary, unique, index)
     */
    public function __construct(
        public string $name   = '',
        public string $index  = 'none' 
    ) {
    }
}
