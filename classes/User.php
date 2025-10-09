<?php
require_once 'Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Register user
    public function register($data) {
        $this->db->query('INSERT INTO users (name, email, password, role, unit_id) VALUES(:name, :email, :password, :role, :unit_id)');
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':unit_id', $data['unit_id']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Login User
    public function login($email, $password) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if($row) {
            $hashed_password = $row->password;
            if(password_verify($password, $hashed_password)) {
                return $row;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Find user by email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        // Bind value
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Check row
        if($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Get User by ID
    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        // Bind value
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        return $row;
    }

    // Get All Users
    public function getUsers() {
        $this->db->query('SELECT u.*, un.name as unit_name 
                         FROM users u 
                         LEFT JOIN units un ON u.unit_id = un.id 
                         ORDER BY u.created_at DESC');
        $results = $this->db->resultSet();

        return $results;
    }

    // Get Users by Role
    public function getUsersByRole($role) {
        $this->db->query('SELECT u.*, un.name as unit_name 
                         FROM users u 
                         LEFT JOIN units un ON u.unit_id = un.id 
                         WHERE u.role = :role 
                         ORDER BY u.created_at DESC');
        $this->db->bind(':role', $role);
        $results = $this->db->resultSet();

        return $results;
    }

    // Get Users by Unit
    public function getUsersByUnit($unit_id) {
        $this->db->query('SELECT u.*, un.name as unit_name 
                         FROM users u 
                         LEFT JOIN units un ON u.unit_id = un.id 
                         WHERE u.unit_id = :unit_id 
                         ORDER BY u.created_at DESC');
        $this->db->bind(':unit_id', $unit_id);
        $results = $this->db->resultSet();

        return $results;
    }

    // Update User
    public function updateUser($data) {
        $this->db->query('UPDATE users SET name = :name, email = :email, role = :role, unit_id = :unit_id WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':unit_id', $data['unit_id']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete User
    public function deleteUser($id) {
        $this->db->query('DELETE FROM users WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update Password
    public function updatePassword($id, $password) {
        $this->db->query('UPDATE users SET password = :password WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':password', $password);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update Profile Image
    public function updateProfileImage($id, $image) {
        $this->db->query('UPDATE users SET profile_image = :image WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':image', $image);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Check if user has specific role
    public function hasRole($user_id, $role) {
        $this->db->query('SELECT role FROM users WHERE id = :id');
        $this->db->bind(':id', $user_id);
        $row = $this->db->single();

        if($row && $row->role == $role) {
            return true;
        } else {
            return false;
        }
    }
}
?>
