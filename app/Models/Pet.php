<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $table = 'pets';

    protected $fillable = [
        'nome',
        'especie',
        'raca',
        'data_nascimento',
        'tutor_id',
        'foto_url',
    ];

    // 👇 Isso diz ao Laravel que um Pet pertence a um Tutor
    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }
}