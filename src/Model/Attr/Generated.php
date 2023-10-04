<?php 

namespace Siktec\Dmm\Model\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Generated {
    public function __construct(
        public bool $value = true
    ) {}
}