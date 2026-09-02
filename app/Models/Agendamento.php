<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $table = 'agendamentos';

    protected $fillable = [
        'data_hora',
        'veterinario_id', // <-- Mudou aqui
        'status',
        'pet_id',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    // 👇 ADICIONE ESTE RELACIONAMENTO 👇
    public function veterinario()
    {
        return $this->belongsTo(Veterinario::class);
    }
}