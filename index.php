<?php 


// autoload
require_once __DIR__ . '/vendor/autoload.php';

use Siktec\Dmm\Server\Manager;
use Siktec\Dmm\Model\Attr;
use Siktec\Dmm\Model;

Manager::defineConnection(
    name : 'main',
    persistant : false,
    host : 'db-one',
    port : 6379
);

Manager::defineConnection(
    name : 'secondary',
    persistant : true,
    host : 'db-two',
    port : 6379
);

// Manager::print();


require_once __DIR__ . '/Users.php';


$noam = new User(
    name : "noam", 
    numerical_age : 15, 
    email_address : "noam@gmail.com",
    friend : null,
    names : [ 
        "noam1", "noam2", "noam3"
    ]
);

// $user = new User(
//     name : "shane", 
//     numerical_age : 42, 
//     email_address : "example@gmail.com",
//     friend : $noam, 
//     names : [ 
//         "shane1", "shane2", "shane3"
//     ]
// );
$user = new User();
$user->fromArray([
    "name"          => "shane",
    "numerical_age" => -2,
    "email_address" => "examp@yahho.com",
    "too_young"     => false,
    "should_be"     => "ignored",
    "friend" => [
        "name"          => "noam234",
        "numerical_age" => 345,
        "email_address" => "example",
        "friend" => [
            "name"          => "yo",
            "numerical_age" => 1414,
            "email_address" => "ex@mail.net"
        ]
    ]
], false);

$validation = $user->_state->validation();

echo $user->_dump->composition() . "\n\n";
// echo $user->_dump->values() . "\n\n";
echo "IS LOADED: " . ($user->_state->isLoaded() ? "TRUE" : "FALSE") . "\n\n";
echo "HAS ERRORS: " . (!empty($validation) ? "TRUE" : "FALSE") . "\n\n";

if ($validation) {
    echo "VALIDATION ERRORS:\n";
    print_r($validation);
    echo "\n\n";
}
print_r($user->toJson(
    external  : true,
    generated : true,
    nested    : true,
    pretty    : true
));


