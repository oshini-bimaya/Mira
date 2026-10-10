<?php

header("Content-Type: application/json");

require "db.php";

$fullName = trim($_POST["fullName"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";

if (
    $fullName === "" ||
    $email === "" ||
    $password === "" ||
    $confirmPassword === ""
) {
    echo json_encode([
        "success" => false,
        "message" => "Please fill in all fields."
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid email."
    ]);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode([
        "success" => false,
        "message" => "Password must contain at least 8 characters."
    ]);
    exit;
}

if ($password !== $confirmPassword) {
    echo json_encode([
        "success" => false,
        "message" => "Passwords do not match."
    ]);
    exit;
}

$check = $conn->prepare(
    "SELECT user_id FROM users WHERE email = ?"
);

$check->bind_param("s", $email);
$check->execute();

$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "An account with this email already exists."
    ]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO users (full_name, email, password, role)
     VALUES (?, ?, ?, 'student')"
);

$stmt->bind_param(
    "sss",
    $fullName,
    $email,
    $hashedPassword
);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Account created successfully!"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Something went wrong. Please try again."
    ]);
}

?>