<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'alias',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Chave estrangeira - Criar relacionamento entre usuário e status
    public function userStatus()
    {
        return $this->belongsTo(UserStatus::class);
    }

    // Relacionamento com despesas
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // Relacionamento com cartões de crédito
    public function creditCards()
    {
        return $this->hasMany(CreditCard::class);
    }

    // Formatar o CPF para imprimir na VIEW
    public function getCpfFormattedAttribute()
    {
        if (!$this->cpf || strlen($this->cpf) !== 11) {
            return $this->cpf;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf);
    }

    // Accessor para retornar a imagem ou uma imagem padrão
    public function getImageUrlAttribute()
    {
        /** @var \Illuminate\Contracts\Filesystem\Cloud $disk */
        $disk = Storage::disk('public');

        // Se não tiver imagem, usa padrão
        if (!$this->image) {
            return asset('/images/users/user.png');
        }

        // Caminho da imagem
        $path = 'users/' . $this->id . '/' . $this->image;

        // Se não encontrar a imagem, carrega a imagem padrão
        if (file_exists(public_path('images/users/' . $this->id . '/' . $this->image))) {
            return asset('images/users/' . $this->id . '/' . $this->image);
        } else {
            return asset('/images/users/user.png');
        }
    }
}
