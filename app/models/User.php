<?php
declare(strict_types=1);

namespace App\models;

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->firstWhere('email', $email);
    }

    public function updateLastLogin(int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET `last_login` = NOW() WHERE `id` = :id");
        return $stmt->execute([':id' => $userId]);
    }

    public function getWithEmployee(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT u.*, e.first_name, e.last_name, e.employee_code, e.department_id, e.designation, e.profile_image, e.phone, e.address, d.name as department_name 
                                    FROM `users` u 
                                    LEFT JOIN `employees` e ON e.user_id = u.id 
                                    LEFT JOIN `departments` d ON d.id = e.department_id 
                                    WHERE u.id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function setResetToken(string $email, string $token, string $expiresAt): bool
    {
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET `reset_token` = :token, `reset_token_expires` = :expires WHERE `email` = :email");
        return $stmt->execute([
            ':token' => $token,
            ':expires' => $expiresAt,
            ':email' => $email,
        ]);
    }

    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `reset_token` = :token AND `reset_token_expires` > NOW() LIMIT 1");
        $stmt->execute([':token' => $token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
