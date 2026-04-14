<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
 protected $fillable = ['name', 'email', 'company_id'];


  public function company(): BelongsTo 
  {
    return $this->belongsTo(Company::class);
  }
}
