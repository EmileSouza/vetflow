<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Veterinario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Estados possíveis da verificação do CRMV
    public const CRMV_PENDENTE = 'pendente';
    public const CRMV_APROVADO = 'aprovado';
    public const CRMV_REJEITADO = 'rejeitado';

    protected $table = 'veterinarios';

    // Somente dados permitidos no preenchimento em massa.
    // Campos administrativos não devem entrar nesta lista.
    protected $fillable = [
        'nome',
        'email',
        'password',
        'crmv',
        'telefone',
        'especialidade',
        'cidade',
        'uf',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'ativo' => 'boolean',
        'crmv_verificado_em' => 'datetime',
    ];

    // Um veterinário pode possuir vários agendamentos.
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    // Administrador responsável pela verificação do CRMV.
    public function verificadoPor()
    {
        return $this->belongsTo(
            User::class,
            'crmv_verificado_por'
        );
    }
}
