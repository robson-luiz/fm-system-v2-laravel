<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UserTermAcceptance extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    // Indicar o nome da tabela
    protected $table = 'user_term_acceptances';

    // Indicar quais colunas podem ser manipuladas
    protected $fillable = [
        'accepted_at',
        'term_version',
        'user_id',
    ];
}
