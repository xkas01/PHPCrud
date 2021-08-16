<?php
include "Database.php";

$obj = new Database();

/*$obj->insert('students', [
    'student_name' => 'Sardor',
    'age' => '20',
    'city' => 'Xor'
]);
print_r($obj->getResult());*/


/*$obj->update('students', [
    'student_name' => 'Sarvar',
    'age' => '19',
    'city' => 'Tosh'
], 'id="7"');
print_r($obj->getResult());*/


/*$obj->delete('students', 'id="6"');
print_r($obj->getResult());*/


/*$obj->sql('select * from students');
print_r($obj->getResult());*/


$obj->select('students', '*', null, null, null, null);
print_r($obj->getResult());