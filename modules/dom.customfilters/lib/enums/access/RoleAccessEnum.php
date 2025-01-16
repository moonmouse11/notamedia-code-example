<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Enums\Access;

enum RoleAccessEnum: string
{
    case DEFAULT = 'default';
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case MANAGER_HEAD = 'manager_head';
    case DIRECTOR = 'director';

    public static function getRoleByValue(string $role): RoleAccessEnum
    {
        return match ($role) {
            'admin' => self::ADMIN,
            'manager_head' => self::MANAGER,
            'head' => self::MANAGER_HEAD,
            'director' => self::DIRECTOR,
            default => self::DEFAULT
        };
    }

    public static function getRoleList(): array
    {
        return array_map(
            callback: static fn ($role) => $role->value,
            array: self::cases()
        );
    }

    public static function getRole(array ...$accesses): string
    {
        $result = self::DEFAULT;

        foreach ($accesses as $access) {
            if(array_key_exists(key: 'role', array:  $access)) {
                $result = $result->getGreaterRole(role: $access['role']);
            }
        }

        return $result->value;
    }

    private function getGreaterRole(string $role): self
    {
        $enum = self::getRoleByValue(role: $role);

        return $this->getRoleRate() >= $enum->getRoleRate() ? $this : $enum;
    }

    private function getRoleRate(): int
    {
        return match ($this){
            self::DEFAULT => 0,
            self::MANAGER => 1,
            self::MANAGER_HEAD => 2,
            self::DIRECTOR => 3,
            self::ADMIN => 4
        };
    }
}
