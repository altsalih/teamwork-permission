<?php

namespace Maosal\TeamworkPermission\Models;

use Illuminate\Database\Eloquent\Model;

class TeamRole extends Model
{
    protected $table = 'team_roles';
    
    protected $fillable = ['team_id', 'name', 'label' ];

    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }
}