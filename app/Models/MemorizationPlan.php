<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemorizationPlan extends Model
{
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
