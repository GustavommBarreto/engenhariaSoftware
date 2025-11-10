<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id', 'nome', 'descricao', 'status', 'inicio', 'fim',
    ];

    protected $casts = [
        'inicio' => 'date',
        'fim'    => 'date',
        // se quiser usar enum forte (Laravel >=10 com PHP 8.1):
        // 'status' => ProjectStatus::class,
    ];

    // Dono (quem criou)
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Membros (com pivot 'role')
    public function members()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('role')
                    ->withTimestamps();
    }
}
