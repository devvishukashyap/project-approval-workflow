<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'status',
        'user_id',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    // Project belongs to a User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Project has one Approval
    public function approval(): HasOne
    {
        return $this->hasOne(Approval::class);
    }

    // Project has many Audit Logs
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
