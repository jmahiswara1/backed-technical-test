<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LeaveRequest extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'start_date',
        'end_date',
        'reason',
        'attachment_path',
        'status',
        'approved_by',
        'total_days',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (LeaveRequest $leaveRequest) {
            if (!$leaveRequest->getKey()) {
                $leaveRequest->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
