<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'receipt_no',
        'uuid',
        'donation_id',
        'donor_name',
        'donor_email',
        'amount',
        'currency',
        'status',
        'pdf_path',
        'issued_at',
        'email_last_sent_at',
        'email_sent_count',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issued_at' => 'datetime',
        'email_last_sent_at' => 'datetime',
    ];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }
}
