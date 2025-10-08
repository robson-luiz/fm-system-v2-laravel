<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Alert extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    // Indicar o nome da tabela
    protected $table = 'alerts';

    // Indicar quais colunas podem ser manipuladas
    protected $fillable = [
        'description',
        'viewed',
        'user_id',
    ];

    /**
     * Casts de atributos
     */
    protected $casts = [
        'viewed' => 'boolean',
    ];

    /**
     * Relacionamento: um e-mail pertence a um usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
