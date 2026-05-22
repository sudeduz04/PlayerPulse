<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixtureImports extends Model
{
    public const STATUS_QUEUED = 'queued';

    public const STATUS_RUNNING = 'running';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUSES = [
        self::STATUS_QUEUED,
        self::STATUS_RUNNING,
        self::STATUS_COMPLETED,
        self::STATUS_FAILED,
    ];

    protected $fillable = [
        'league_id',
        'user_id',
        'original_filename',
        'file_path',
        'source',
        'status',
        'created_rows',
        'skipped_rows',
        'skipped_payload',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'skipped_payload' => 'array',
            'created_rows' => 'integer',
            'skipped_rows' => 'integer',
        ];
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(Leagues::class, 'league_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isFinished(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_FAILED], true);
    }
}
