<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Veterinario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'veterinarios';

    protected $fillable = [
        'nome',
        'email',
        'password',
        'crmv',
        'telefone',
        'especialidade',
        'ativo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Isso criptografa a senha automaticamente ao salvar!
        'ativo' => 'boolean',
    ];

    // Um veterinário tem muitos agendamentos
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }
}
