<?php
// Script to seed the database with an initial Super Admin

require_once __DIR__ . '/db.php';

// Admin Details
$admin_name = 'Super Admin';
$email = 'admin@ayyatulhijab.com';
$password = 'admin123'; // Default password
$role = 'super_admin';

// Check if admin already exists
$check_sql = "SELECT admin_id FROM admins WHERE email = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "Admin user already exists.\n";
}
else {
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert Admin
    $insert_sql = "INSERT INTO admins (admin_name, email, password, role) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ssss", $admin_name, $email, $hashed_password, $role);

    if ($insert_stmt->execute()) {
        echo "Super Admin created successfully!\n";
        echo "Email: " . $email . "\n";
        echo "Password: " . $password . "\n";
    }
    else {
        echo "Error creating admin: " . $insert_stmt->error . "\n";
    }
    $insert_stmt->close();
}

$stmt->close();
$conn->close();
?>
