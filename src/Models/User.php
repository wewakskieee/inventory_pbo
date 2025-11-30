<?php
namespace App\Models;

class User extends BaseModel {
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private string $role;
    
    protected function getTableName(): string {
        return 'users';
    }
    
    public function getId(): int {
        return $this->id;
    }
    
    public function setId(int $id): void {
        $this->id = $id;
    }
    
    public function getUsername(): string {
        return $this->username;
    }
    
    public function setUsername(string $username): void {
        $this->username = $username;
    }
    
    public function getEmail(): string {
        return $this->email;
    }
    
    public function setEmail(string $email): void {
        $this->email = $email;
    }
    
    public function getPassword(): string {
        return $this->password;
    }
    
    public function setPassword(string $password): void {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
    
    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }
    
    public function getRole(): string {
        return $this->role;
    }
    
    public function setRole(string $role): void {
        $this->role = $role;
    }
    
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }
}
