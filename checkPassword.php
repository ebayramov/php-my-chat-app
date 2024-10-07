<?php

$hash = '$2y$10$TzsEvcPX4q1HDhaSroKCZeBFst76dVQl64jtLBOK/OJILXm/00wS6';
$password = 'salam';

if (password_verify($password, $hash)) {
    echo 'Password is correct!';
} else {
    echo 'Password is incorrect!';
}
?>