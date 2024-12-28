<?php

namespace App\Models;

use App\Enums\EventTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $invoice_id
 * @property int $user_id
 * @property Carbon $event_date
 * @property EventTypeEnum $event_type
 * @property float $price
 * @property ?int $parent_invoice_item_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class InvoiceItem extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'event_type' => EventTypeEnum::class,
            'price' => 'float',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parentInvoiceItem(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class, 'parent_invoice_item_id');
    }
}
