<?php

namespace App\Models;

use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemorizationPlan extends Model
{
    use HasFactory, AuditableTrait;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function planItems()
    {
        return $this->hasMany(PlanItem::class, "plan_id", "id");
    }
}
