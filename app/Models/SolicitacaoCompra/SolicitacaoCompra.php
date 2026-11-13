<?php

namespace App\Models\SolicitacaoCompra;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitacaoCompra extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_ENVIADO_DIRETORIA = 'enviado_diretoria';
    public const STATUS_APROVADO_DIRETORIA = 'aprovado_diretoria';
    public const STATUS_REJEITADO_DIRETORIA = 'rejeitado_diretoria';
    public const STATUS_ANALISE_PROJETOS = 'analise_projetos';
    public const STATUS_RECURSOS_DISPONIVEIS_COMPRAS = 'recursos_disponiveis_compras';
    public const STATUS_RECURSOS_FUNDACAO = 'recursos_fundacao';
    public const STATUS_CONCLUIDO = 'concluido';
    public const STATUS_CANCELADO = 'cancelado';

    protected $table = 'solicitacoes_compra';

    protected $fillable = [
        'titulo',
        'valor',
        'prazo',
        'descricao',
        'usuario_id',
        'status',
        'prioridade',
        'motivo_rejeicao',
        'recursos_existem',
        'status_compra',
        'aprovado_cancelado_por',
        'concluido_por',
        'diretoria_por',
        'projetos_por',
        'compras_por',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'integer',
            'recursos_existem' => 'boolean',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function aprovadoCanceladoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'aprovado_cancelado_por');
    }

    public function concluidoPorUsuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'concluido_por');
    }

    public function diretoriaPorUsuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'diretoria_por');
    }

    public function projetosPorUsuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'projetos_por');
    }

    public function comprasPorUsuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'compras_por');
    }
}
