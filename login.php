<?php

header("Content-Type: application/json");

require "db.php";

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    echo json_encode([
        "success" => false,
        "message" => "Please enter your email and password."
    ]);
    exit;
}

$stmt = $conn->prepare(
    "SELECT user_id, full_name, email, password, role
     FROM users
     WHERE email = ?"
);

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $user = $result->fetch_assoc();

    if (password_verify($password, $user["password"])) {

        echo json_encode([
            "success" => true,
            "message" => "Login successful!"
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Invalid email or password."
        ]);
    }

} else {

    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);
}

?>