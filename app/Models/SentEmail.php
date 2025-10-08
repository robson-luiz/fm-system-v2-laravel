<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SentEmail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    // Indicar o nome da tabela
    protected $table = 'sent_emails';

    // Indicar quais colunas podem ser manipuladas
    protected $fillable = [
        'subject',
        'body',
        'recipient_email',
        'sent_at',
        'user_id',
    ];

    /**
     * Casts de atributos
     */
    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Relacionamento: um e-mail pertence a um usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
