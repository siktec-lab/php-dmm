<?php 

use Siktec\Dmm\Model\Attr;
use Siktec\Dmm\Model;

#[Attr\Connection(name: 'main')]
class User extends Model\Structure {

    #[Attr\Property] 
    public ?string $name;

    #[Attr\Property(name: 'age')]
    public int $numerical_age = 21; // default value can be set here

    #[Attr\Property(name: 'email')]
    public string $email_address;

    #[Attr\Property, Attr\Generated]
    public bool $too_young; // This property will not be saved its calculated

    #[Attr\Property]
    public ?User $friend;

    #[Attr\Property]
    public array $names = [];

    #[Attr\Property, Attr\Generated]
    public ?string $message = null;

    public const MINIMUM_AGE = 18;

    public function __construct(
        ?string $name          = null,
        ?int    $numerical_age = null,
        ?string $email_address = null,
        ?User   $friend        = null,
        ?array  $names         = null
    ) {
        parent::__construct(get_defined_vars());
    }

    protected function load(array $data): void
    {
        // name is a required field
        $name = trim($data['name'] ?? "");
        if (mb_strlen($name) > 2) {
            
            $this->name = $data['name'];
            $this->message = "Hello {$this->name}";

        } else {
            $this->_state->invalid(
                "name", "required and must be at least 3 characters long"
            );
        }

        //age is an optional positive integer or 0
        $age = intval($data['numerical_age'] ?? 0);
        if ($age >= 0) {
            $this->numerical_age = $age;
            $this->too_young = $age < self::MINIMUM_AGE;
        } else {
            $this->_state->invalid(
                "age", "must be a positive integer or 0"
            );
        }

        // email is a required field
        $email = trim($data['email_address'] ?? "");
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email_address = $email;
        } else {
            $this->_state->invalid(
                "email", "required and must be a valid email address"
            );
        }

        // Set friend if provided
        $this->friend = $data['friend'] ?? null;

        // Set names if provided
        if ($data['names'] ?? false) {
            $this->names = $data['names'];
        }

    }

}




