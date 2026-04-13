<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    use HasFactory;
    protected $fillable = [
        'contact_id',
        'template_id',
        'frequency_type',
        'send_times',
        'channel',
    ];

    protected $casts = [
        'send_times' => 'array',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function shouldSendAt(string $time): bool
    {
        return in_array($time, $this->send_times ?? []);
    }
}
