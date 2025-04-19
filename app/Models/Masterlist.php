<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Masterlist extends Model
{
    protected $fillable = [
        'firstName',
        'middleName',
        'familyName',
        'nameExtension',
        'barangay',
        'precinctNo',
        'leader'
    ];

    public function members()
    {
        return $this->belongsToMany(
            Masterlist::class,
            'member_lists',
            'leaderId',
            'memberId'
        );
    }

    public function membersHome()
    {
        // leader → many members
        return $this->hasMany(MemberList::class, 'leaderId')
                    ->with('member');
    }

    public function leaders()
    {
        return $this->belongsToMany(
            self::class,       // related model
            'member_lists',    // pivot table
            'memberId',        // this model’s key on pivot
            'leaderId'         // related model’s key on pivot
        );
    }
    
}
