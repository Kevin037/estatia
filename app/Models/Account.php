<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the parent account
     */
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Get child accounts
     */
    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Get all journal entries
     */
    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    /**
     * Get journal entries with filters
     * 
     * @param string $type The column name to check for not null (e.g., 'debit', 'credit')
     * @param string $dt_start Start date
     * @param string|null $dt_end End date (optional)
     * 
     * Usage examples:
     * - $account->journal_entries('debit', '2024-01-01') // Entries with debit before 2024-01-01
     * - $account->journal_entries('credit', '2024-01-01', '2024-12-31') // Entries with credit in date range
     */
    public function journal_entries($type, $dt_start, $dt_end = null)
    {
        $query = $this->hasMany(JournalEntry::class)
            ->whereNotNull($type);

        if ($dt_end === null) {
            // If no end date, get entries before start date
            $query->where('dt', '<', $dt_start);
        } else {
            // If end date provided, get entries between dates
            $query->whereBetween('dt', [$dt_start, $dt_end]);
        }

        return $query;
    }

    /**
     * Scope to search accounts
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%");
        });
    }

    /**
     * Get root accounts
     */
    public static function getRootAccounts()
    {
        return static::whereNull('parent_id')->get();
    }
}
