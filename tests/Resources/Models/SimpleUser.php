<?php

namespace Siktec\Dmm\Tests\Resources\Models;

use Siktec\Dmm\Model\Attr;
use Siktec\Dmm\Model;

#[Attr\Connection]
class SimpleUser extends Model\Structure
{
    #[Attr\Property]
    public ?string $name = null;

    #[Attr\Property]
    public int $age = 0;

    #[Attr\Property]
    public ?string $email = null;

    #[Attr\Property, Attr\Generated]
    public string $greet = "Hello";

    public function __construct(
        ?string $name   = null,
        ?int    $age    = null,
        ?string $email  = null
    ) {
        parent::__construct(get_defined_vars());
    }

    protected function load(array $data): void
    {

        // name is a required field
        $name  = trim($data['name'] ?? "");
        $email = trim($data['email'] ?? "");
        $age   = $data['age'] ?? null;

        //Validate the name:
        if (mb_strlen($name) > 2) {
            $this->name = $data['name'];
            $this->greet = "Hello {$this->name}";
        } else {
            $this->state->invalid("name", "required and must be at least 3 characters long");
        }

        //Validate the email:
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        } else {
            $this->state->invalid("email", "required and must be a valid email address");
        }

        //Validate the age:
        if (is_int($age) && $age >= 0) {
            $this->age = $age;
        } else {
            $this->state->invalid("age", "required and must be a positive integer or 0");
        }
    }
}
