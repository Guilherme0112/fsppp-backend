<?php

namespace App\Models\SolicitacaoPagamento;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitacaoPagamento extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDENTE = 'pendente';

    public const STATUS_APROVADO = 'aprovado';

    public const STATUS_CONCLUIDO = 'concluido';

    public const STATUS_CANCELADO = 'cancelado';

    protected $table = 'solicitacoes_pagamento';

    protected $fillable = [
        'titulo',
        'valor',
        'descricao',
        'usuario_id',
        'status',
        'aprovado_cancelado_por',
        'concluido_por',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'integer',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Named *PorUsuario (not aprovadoCanceladoPor/concluidoPor) so the relation's
    // snake-cased JSON key doesn't collide with the raw aprovado_cancelado_por /
    // concluido_por FK columns and silently overwrite them in toArray()/JSON output.
    public function aprovadoCanceladoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'aprovado_cancelado_por');
    }

    public function concluidoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'concluido_por');
    }
}
