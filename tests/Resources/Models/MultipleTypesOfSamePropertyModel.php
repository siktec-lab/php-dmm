<?php

namespace Siktec\Dmm\Tests\Resources\Models;

use Siktec\Dmm\Model\Attr;
use Siktec\Dmm\Model;

#[Attr\Connection(name: "primary")]
class MultipleTypesOfSamePropertyModel extends Model\Structure
{
    #[Attr\Property]
    public string|int $name = "bob";

    public function __construct(
        ?string $name = null
    ) {
        parent::__construct(get_defined_vars());
    }

    protected function load(array $data): void
    {
        return;
    }
}
