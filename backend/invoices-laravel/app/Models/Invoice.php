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

    // Relación: Una factura pertenece a un cliente
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relación: Una factura tiene muchos items
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Calcular totales automáticamente
    public function calculateTotals()
    {
        $this->subtotal = $this->items()->sum('line_total');
        $this->tax_amount = round(($this->subtotal * $this->tax_rate) / 100, 2);
        $this->total_amount = $this->subtotal + $this->tax_amount;
        return $this;
    }
}
