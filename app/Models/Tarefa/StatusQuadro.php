<?php

namespace App\Models\Tarefa;

use App\Models\ModelBase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusQuadro extends ModelBase
{
    protected $table = 'status_quadro';

    protected $fillable = [
        'nome',
        'descricao',
        'quadro_id',
    ];

    protected $casts = [
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
        'apagado_em' => 'datetime',
    ];

    const CREATED_AT = 'criado_em';

    const UPDATED_AT = 'atualizado_em';

    const DELETED_AT = 'apagado_em';

    public function quadro(): BelongsTo
    {
        return $this->belongsTo(Quadro::class, 'quadro_id');
    }

    public function tarefas(): HasMany
    {
        return $this->hasMany(Tarefa::class, 'status_id');
    }
}
