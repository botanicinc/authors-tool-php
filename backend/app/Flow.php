<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Flow extends Model implements AuthenticatableContract, AuthorizableContract
{

    use Authenticatable,
        Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'flow'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
    
    /**
     * Authorization check
     * 
     * @param int $flowId Flow id
     * @return boolean 
     */    
    public static function userHasAccess($flowId)
    {
        if (strpos(env('ANONYMOUS_ACCESS_IP'), $_SERVER['REMOTE_ADDR']) !== false) {
            return true;
        }
        
        if (!Auth::user()) { 
            return false;
        }
        
        $userProjects = Auth::user()->organization->projects;
        $flow = Flow::find($flowId, ['project_id']);
        foreach ($userProjects as $up) {
            if ($up->id == $flow->project_id) {
                return true;
            }
        }

        return false;
    }

}
