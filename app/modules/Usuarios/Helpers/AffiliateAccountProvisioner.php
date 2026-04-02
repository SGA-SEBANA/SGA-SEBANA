<?php

namespace App\Modules\Usuarios\Helpers;

use App\Modules\Usuarios\Models\Role;
use App\Modules\Usuarios\Models\User;
use Throwable;

class AffiliateAccountProvisioner
{
    private User $userModel;
    private Role $roleModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->roleModel = new Role();
    }

    public function provision(array $data): array
    {
        try {
            $cedula = trim((string) ($data['cedula'] ?? ''));
            if ($cedula === '') {
                return [
                    'success' => false,
                    'created' => false,
                    'error' => 'No se pudo crear usuario automatico: cedula vacia.'
                ];
            }

            $baseUsername = $this->buildUsernameSeed($cedula);
            $preferredEmail = $this->buildPreferredEmail((string) ($data['correo'] ?? ''), $baseUsername);

            $existing = $this->findExistingUser($baseUsername, $preferredEmail);
            if ($existing) {
                return [
                    'success' => true,
                    'created' => false,
                    'user_id' => (int) ($existing['id'] ?? 0),
                    'username' => (string) ($existing['username'] ?? ''),
                    'correo' => (string) ($existing['correo'] ?? '')
                ];
            }

            $roleId = $this->resolveAffiliateRoleId();
            if ($roleId <= 0) {
                return [
                    'success' => false,
                    'created' => false,
                    'error' => 'No se encontro un rol activo para afiliados (Consulta).'
                ];
            }

            $username = $this->resolveUniqueUsername($baseUsername);
            $correo = $this->resolveUniqueEmail($preferredEmail, $username);
            $tempPassword = $this->generateTemporaryPassword();

            $nombreCompleto = trim(
                ((string) ($data['nombre'] ?? '')) . ' ' .
                ((string) ($data['apellido1'] ?? '')) . ' ' .
                ((string) ($data['apellido2'] ?? ''))
            );

            $userId = (int) $this->userModel->create([
                'username' => $username,
                'correo' => $correo,
                'contrasena' => SecurityHelper::hashPassword($tempPassword),
                'rol_id' => $roleId,
                'nombre_completo' => $nombreCompleto !== '' ? $nombreCompleto : null,
                'telefono' => trim((string) ($data['telefono'] ?? '')) ?: null,
                'estado' => 'activo',
                'debe_cambiar_contrasena' => true,
            ]);

            return [
                'success' => true,
                'created' => true,
                'user_id' => $userId,
                'username' => $username,
                'correo' => $correo,
                'temp_password' => $tempPassword
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'created' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function findExistingUser(string $username, string $email): ?array
    {
        $byUsername = $this->userModel->findByUsername($username);
        if ($byUsername) {
            return $byUsername;
        }

        if ($email !== '') {
            $byEmail = $this->userModel->findByEmail($email);
            if ($byEmail) {
                return $byEmail;
            }
        }

        return null;
    }

    private function resolveAffiliateRoleId(): int
    {
        $consulta = $this->roleModel->findByName('Consulta');
        if ($consulta && (int) ($consulta['id'] ?? 0) > 0 && (int) ($consulta['activo'] ?? 1) === 1) {
            return (int) $consulta['id'];
        }

        foreach ($this->roleModel->getActive() as $role) {
            $nivel = strtolower(trim((string) ($role['nivel_acceso'] ?? '')));
            if ($nivel === 'basico') {
                return (int) $role['id'];
            }
        }

        return 0;
    }

    private function buildUsernameSeed(string $cedula): string
    {
        $seed = preg_replace('/[^A-Za-z0-9]/', '', strtolower($cedula)) ?? '';
        if ($seed === '') {
            $seed = 'afiliado' . date('YmdHis');
        }

        return $seed;
    }

    private function buildPreferredEmail(string $correo, string $username): string
    {
        $correo = strtolower(trim($correo));
        if ($correo !== '' && filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return $correo;
        }

        return 'afiliado.' . $username . '@sebana.local';
    }

    private function resolveUniqueUsername(string $base): string
    {
        $candidate = $base;
        $suffix = 1;

        while ($this->userModel->existsUsername($candidate)) {
            $candidate = $base . '_' . $suffix;
            $suffix++;
            if ($suffix > 9999) {
                $candidate = $base . '_' . bin2hex(random_bytes(2));
                break;
            }
        }

        return $candidate;
    }

    private function resolveUniqueEmail(string $preferred, string $username): string
    {
        $email = strtolower(trim($preferred));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = 'afiliado.' . $username . '@sebana.local';
        }

        if (!$this->userModel->existsEmail($email)) {
            return $email;
        }

        $parts = explode('@', $email, 2);
        $local = preg_replace('/[^a-z0-9._-]/i', '', $parts[0] ?? '') ?: ('afiliado.' . $username);
        $domain = preg_replace('/[^a-z0-9.-]/i', '', $parts[1] ?? '') ?: 'sebana.local';

        $suffix = 1;
        do {
            $candidate = $local . '+' . $suffix . '@' . $domain;
            $suffix++;
        } while ($this->userModel->existsEmail($candidate) && $suffix < 9999);

        return $candidate;
    }

    private function generateTemporaryPassword(): string
    {
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnopqrstuvwxyz';
        $numbers = '23456789';
        $symbols = '!@#$%*';
        $all = $upper . $lower . $numbers . $symbols;

        $chars = [
            $this->randomChar($upper),
            $this->randomChar($lower),
            $this->randomChar($numbers),
            $this->randomChar($symbols),
        ];

        for ($i = 0; $i < 8; $i++) {
            $chars[] = $this->randomChar($all);
        }

        shuffle($chars);
        return implode('', $chars);
    }

    private function randomChar(string $pool): string
    {
        $index = random_int(0, strlen($pool) - 1);
        return $pool[$index];
    }
}
