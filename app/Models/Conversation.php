<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    /** @use HasFactory<\Database\Factories\ConversationFactory> */
    use HasFactory;

    public function dataset() { return $this->belongsTo(Dataset::class); }
    public function messages() { return $this->hasMany(Message::class); }
}
