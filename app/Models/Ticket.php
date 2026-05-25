<?php

namespace App\Models;

use App\Enums\Impact;
use App\Enums\Priority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'assigned_to',
        'category_id',
        'subcategory_id',
        'asset_id',
        'title',
        'description',
        'status',
        'priority',
        'impact',
        'resolution',
        'root_cause',
        'prevention',
        'weight_score',
        'started_at',
        'resolved_at',
        'closed_at',
        'sla_deadline',
        'is_sla_breached',
        'reviewed_by',
        'review_notes',
        'reopened_at',
        'reopen_count',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'priority' => Priority::class,
            'impact' => Impact::class,
            'weight_score' => 'decimal:2',
            'started_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'sla_deadline' => 'datetime',
            'is_sla_breached' => 'boolean',
            'reopened_at' => 'datetime',
            'reopen_count' => 'integer',
        ];
    }

    // ─── Relationships ───

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class)->orderBy('created_at', 'desc');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TicketHistory::class)->orderBy('created_at', 'desc');
    }

    // ─── Scopes ───

    public function scopeOpen($query)
    {
        return $query->where('status', TicketStatus::Open);
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByStatus($query, TicketStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('is_sla_breached', true);
    }

    public function scopeCreatedBetween($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    // ─── Helpers ───

    public function isOverdue(): bool
    {
        if (!$this->sla_deadline) {
            return false;
        }
        return now()->greaterThan($this->sla_deadline) && !in_array($this->status, [TicketStatus::Closed]);
    }

    public function canBeReopened(): bool
    {
        if ($this->status !== TicketStatus::Closed || !$this->closed_at) {
            return false;
        }
        return $this->closed_at->addDays(7)->isFuture();
    }

    public function getResolutionTimeAttribute(): ?string
    {
        if (!$this->started_at || !$this->resolved_at) {
            return null;
        }
        $diff = $this->started_at->diff($this->resolved_at);
        if ($diff->days > 0) {
            return $diff->days . 'd ' . $diff->h . 'h';
        }
        return $diff->h . 'h ' . $diff->i . 'm';
    }

    public function getLatestProgressAttribute(): int
    {
        return $this->activities()->max('progress_percentage') ?? 0;
    }

    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $date = now()->format('Ymd');
        $lastTicket = static::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastTicket) {
            $lastNumber = (int) substr($lastTicket->ticket_number, -4);
            $sequence = $lastNumber + 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }
}
