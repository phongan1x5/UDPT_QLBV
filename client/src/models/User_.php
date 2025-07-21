<?php
class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function login($username, $password)
    {
        $query = "SELECT user_id, username, email, password, status, user_type 
                  FROM users WHERE username = ? AND status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check if password is hashed (length > 50) or plain text
            if (strlen($user['password']) > 50) {
                // Password is hashed, use password_verify
                if (password_verify($password, $user['password'])) {
                    unset($user['password']); // Remove password from returned data
                    return $user;
                }
            } else {
                // Password is plain text, use direct comparison
                if ($password === $user['password']) {
                    unset($user['password']); // Remove password from returned data
                    return $user;
                }
            }
        }

        return false;
    }

    public function register($username, $email, $password)
    {
        $query = "INSERT INTO users (username, email, password, user_type, status) 
                  VALUES (?, ?, ?, 'member', 'active')";

        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt->bindParam(1, $username);
        $stmt->bindParam(2, $email);
        $stmt->bindParam(3, $hashedPassword);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    public function usernameExists($username)
    {
        $query = "SELECT user_id FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function emailExists($email)
    {
        $query = "SELECT user_id FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getUserById($userId)
    {
        $query = "SELECT user_id, username, email, user_type, status FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $userId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUserEmail($userId, $newEmail)
    {
        $query = "UPDATE users SET email = ?
        WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $newEmail);
        $stmt->bindParam(2, $userId);
        return $stmt->execute();
    }
}
