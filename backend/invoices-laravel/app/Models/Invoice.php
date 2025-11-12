<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'invoice_number',
        'status',
        'issue_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'total_amount',
        'notes',
        'currency',
        'tax_rate',
    ];

    protected $casts = [
        'issue_date' => 'datetime',
        'due_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
    ];

    // Relationship: An invoice belongs to a customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship: An invoice has many items
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Calculate totals automatically
    public function calculateTotals()
    {
        $this->subtotal = $this->items()->sum('line_total');
        $this->tax_amount = round(($this->subtotal * $this->tax_rate) / 100, 2);
        $this->total_amount = $this->subtotal + $this->tax_amount;
        return $this;
    }

    /**
     * Mark the invoice as paid.
     *
     * @return bool
     */
    public function markAsPaid(): bool
    {
        if ($this->status === 'paid') {
            throw new \Exception('This invoice is already marked as paid');
        }

        if ($this->status === 'cancelled') {
            throw new \Exception('Cannot mark a cancelled invoice as paid');
        }

        return $this->update(['status' => 'paid']);
    }

    /**
     * Cancel the invoice (anulate, not delete).
     *
     * @return bool
     */
    public function cancel(): bool
    {
        if (!in_array($this->status, ['draft', 'sent'])) {
            throw new \Exception('Only invoices in draft or sent status can be cancelled');
        }

        return $this->update(['status' => 'cancelled']);
    }

    /**
     * Check if invoice can be edited.
     *
     * @return bool
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft']);
    }

    /**
     * Check if invoice can be cancelled.
     *
     * @return bool
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['draft', 'sent']);
    }
}

