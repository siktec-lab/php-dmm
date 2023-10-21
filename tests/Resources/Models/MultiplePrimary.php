<?php

namespace Siktec\Dmm\Tests\Resources\Models;

use Siktec\Dmm\Model\Attr;

#[Attr\Connection]
class MultiplePrimary extends SimpleUser
{
    #[Attr\Property(index : 'primary')]
    public ?string $social_id = null;

    public function __construct(
        ?string $social_id = null,
        ?string $name      = null,
        ?int    $age       = null,
        ?string $email     = null
    ) {
        parent::__construct(
            name  : $name,
            age   : $age,
            email : $email
        );

        $this->social_id = $social_id;
    }
}
