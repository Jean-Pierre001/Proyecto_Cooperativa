<?php
include 'includes/conn.php'; // Asegurate que tu clase Database esté aquí

try {
    $conn = $pdo;

    $email = 'admin@admin';
    $password = password_hash('123', PASSWORD_DEFAULT); // Hashear contraseña
    $type = 1; // admin
    $first_name = 'Admin';
    $last_name = 'User';
    $address = 'Admin Address';
    $contact_info = '1234567890';
    $photo = ''; // Puede quedar vacío o un valor por defecto
    $created_on = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO users (email, password, type, first_name, last_name, address, contact_info, photo, created_on) 
                            VALUES (:email, :password, :type, :first_name, :last_name, :address, :contact_info, :photo, :created_on)");

    $stmt->execute([
        ':email' => $email,
        ':password' => $password,
        ':type' => $type,
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':address' => $address,
        ':contact_info' => $contact_info,
        ':photo' => $photo,
        ':created_on' => $created_on
    ]);

    echo "Admin user created successfully.";

    $pdo->close();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
