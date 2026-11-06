<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Papéis administrativos permitidos
    public const PAPEL_ADMIN = 'admin';
    public const PAPEL_INVESTIDOR = 'investidor';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean',
        ];
    }

    /**
     * Verifica se pode acessar a Central Admin.
     */
    public function podeAcessarAdministracao(): bool
    {
        return $this->ativo === true
            && in_array($this->papel, [
                self::PAPEL_ADMIN,
                self::PAPEL_INVESTIDOR,
            ], true);
    }

    /**
     * Somente a administradora ativa pode aprovar CRMV.
     */
    public function podeAprovarCrmv(): bool
    {
        return $this->podeAcessarAdministracao()
            && $this->papel === self::PAPEL_ADMIN;
    }
}
