<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberList extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * (Optional if you follow Laravel’s pluralization: member_lists)
     *
     * @var string
     */
    protected $table = 'member_lists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'leaderId',
        'memberId',
    ];

    /**
     * Get the leader Masterlist record.
     */
    public function leader()
    {
        return $this->belongsTo(Masterlist::class, 'leaderId');
    }

    /**
     * Get the member Masterlist record.
     */
    public function member()
    {
        return $this->belongsTo(Masterlist::class, 'memberId');
    }
}
