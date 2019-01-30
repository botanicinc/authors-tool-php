<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Project extends Model implements AuthenticatableContract, AuthorizableContract
{

    use Authenticatable,
        Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'organization_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * Returns project's flows id/name
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function flowsNameId()
    {
        return $this->hasMany('App\Flow')->select(['id', 'name']);
    }

    /**
     * Returns project's flows objects
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function flows()
    {
        return $this->hasMany('App\Flow');
    }

    /**
     * Authorization check
     * 
     * @param int $projectId Project id
     * @return boolean
     */
    public static function userHasAccess($projectId)
    {
        if (strpos(env('ANONYMOUS_ACCESS_IP'), $_SERVER['REMOTE_ADDR']) !== false) {
            return true;
        }

        if (!Auth::user()) {
            return false;
        }

        $userProjects = Auth::user()->organization->projects;
        foreach ($userProjects as $up) {
            if ($up->id == $projectId) {
                return true;
            }
        }
            $userProjects = Auth::user()->organization->projects;
            foreach ($userProjects as $up) {
                if ($up->id == $projectId) {
                    return true;
                }
            }
        
        return false;
    }

}
