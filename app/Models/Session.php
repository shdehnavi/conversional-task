<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property ?Carbon $activated_at
 * @property ?Carbon $appointment_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Session extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'activated_at' => 'date',
            'appointment_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
