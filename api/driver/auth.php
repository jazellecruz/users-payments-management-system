<?php 

session_start();

require_once __DIR__ . '/../../db/db_conn.php';
require_once __DIR__ . '/../../queries/accounts.php';
require_once __DIR__ . '/../../utils/auth.php';
require_once __DIR__ . '/../../utils/utils.php';

$conn = getDBConnection();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action']) && $_POST['action'] === 'driver_signup') {
        
        $firstName = sanitizeData($conn, $_POST['first_name']) ?? '';
        $lastName = sanitizeData($conn, $_POST['last_name']) ?? '';
        $email = sanitizeData($conn, $_POST['email']);
        $password = sanitizeData($conn, $_POST['password']);
        $role = sanitizeData($conn, $_POST['role']);

        // check first if user already exists in the database
        $existingUser = getUserByEmailAndRole($conn, $email, 'driver');

        if(!empty($existingUser)) {
            echo "User with this email already exists.";
            exit;
        }
        $hashedPassword = hashPassword($password);

        $user = [
            'email' => $email,
            'password' => $hashedPassword,
            'role' =>  $role,
            'firstName' => $firstName,
            'lastName' => $lastName
        ];

        $createUser = createUserAccount($conn, $user);

        if($createUser) {
            $lastid = $conn->insert_id;
            $newUser = getUserAccById($conn, $lastid);
            $userData = [
                'user_id' => $newUser['user_id'],
                'email' => $newUser['email'],
                'first_name' => $newUser['first_name'],
                'last_name' => $newUser['last_name'],
                'role' => $newUser['role']
            ];
            // optional: add unverified status of driver in session
            generateSession($userData);
            redirectUser('../../views/driver/onboarding/onboarding.php');
        } else {
            echo "Error creating account. Please try again.";
            exit;
        }
    }
}


?>