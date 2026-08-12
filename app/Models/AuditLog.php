<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // ✅ यह Import ज़रूरी है

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'action',
        'details',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'details' => 'array', // ✅ JSON को Array में बदलने के लिए (अच्छा practice)
    ];

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project that was acted upon.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}