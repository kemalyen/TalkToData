<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    /** @use HasFactory<\Database\Factories\DatasetFactory> */
    use HasFactory;

    protected $fillable = ['name', 'file_path', 'schema_json', 'row_count'];

    protected $casts = ['schema_json' => 'array'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}
