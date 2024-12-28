<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $customer_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property float $total_price
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Invoice extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_price' => 'float',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function registrationEvents(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Customer::class, 'id', 'customer_id', 'customer_id', 'id');
    }

    public function activationEvents(): HasManyThrough
    {
        return $this->hasManyThrough(Session::class, User::class, 'customer_id', 'user_id', 'customer_id', 'id');
    }

    public function appointmentEvents(): HasManyThrough
    {
        return $this->hasManyThrough(Session::class, User::class, 'customer_id', 'user_id', 'customer_id', 'id');
    }
}
