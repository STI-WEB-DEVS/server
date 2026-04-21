<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasUuids;
    
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    protected $fillable = ['name', 'code'];
}
