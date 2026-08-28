<?php

namespace App\Models\AccuSoft;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class JournalEntryDetail extends Model
{
    use HasFactory;


     protected $table = 'journal_entry_details';
    protected $fillable = [
        'journal_entry_id',
        'tree_account_id',
        'cost_center_id',
        'debit',
        'credit',
        'description',
        'is_locked',
        'locked_at',
        'locked_by',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];

    // Relations
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function treeAccount(): BelongsTo
    {
        return $this->belongsTo(TreeAccounts::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenters::class);
    }

    public function locker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    // Helper Methods
    public function isDebit(): bool
    {
        return $this->debit > 0;
    }

    public function isCredit(): bool
    {
        return $this->credit > 0;
    }

    public function getAmount(): float
    {
        return $this->isDebit() ? $this->debit : $this->credit;
    }

   
}
