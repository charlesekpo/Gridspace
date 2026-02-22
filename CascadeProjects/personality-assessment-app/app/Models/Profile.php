<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'questionnaire_answers',
        'personality_analysis',
        'big_five_scores',
        'strengths',
        'development_areas',
        'work_style_preferences',
        'analyzed_at',
    ];

    protected $casts = [
        'questionnaire_answers' => 'array',
        'big_five_scores' => 'array',
        'analyzed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
