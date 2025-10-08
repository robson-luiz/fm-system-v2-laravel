<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UserStatus extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    // Indicar o nome da tabela
    protected $table = 'user_statuses';

    // Indicar quais colunas podem ser manipuladas
    protected $fillable = ['name'];

    // Chave primária - Criar relacionamento entre usuário e status
    public function user()
    {
        return $this->hasMany(User::class);
    }
}
