<?php

namespace App\Models;

use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    /**
     * Named rather than guarded. Everything here arrives from a form a
     * stranger can post to, so the safe default is that a column is not
     * writable until somebody decides it should be. status and read_at are
     * the point: both are set by us, and neither should ever be settable by
     * the person filling in the form.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'organisation',
        'subject',
        'message',
        'status',
        'locale',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'read_at' => 'datetime',
        ];
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead(): void
    {
        if ($this->read_at !== null) {
            return;
        }

        $this->forceFill([
            'read_at' => now(),
            'status' => $this->status === LeadStatus::New ? LeadStatus::Read : $this->status,
        ])->save();
    }
}
