<?php

namespace Siktec\Dmm\Model\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Property
{
    public function __construct(
        public string $name = ''
    ) {
    }
}
