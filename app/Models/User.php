<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\ReportNote;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLE_PELAPOR     = 'pelapor';
    public const ROLE_PEMANTAU    = 'pemantau';
    public const ROLE_KOORDINATOR = 'koordinator';

    protected $primaryKey = 'id_user';

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed'
        ];
    }

    public function isPelapor(): bool     { return $this->role === self::ROLE_PELAPOR; }
    public function isPemantau(): bool    { return $this->role === self::ROLE_PEMANTAU; }
    public function isKoordinator(): bool { return $this->role === self::ROLE_KOORDINATOR; }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'id_user', 'id_user');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ReportNote::class, 'id_user', 'id_user');
    }

    public function forwardings(): HasMany
    {
        return $this->hasMany(ReportForwarding::class, 'id_user', 'id_user');
    }
}