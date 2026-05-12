<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Company;
use App\Models\DianaSoftMenuOption;
use App\Models\Gestiune;
use App\Models\Permission;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','telefon','blocat','user_type','functia','status','link_poza',
        'program_de_lucru','data_expirare_parola','departament','sex','selectat','grup','data_expirare_parola'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'grup'=>'array',
        'gestiuni.pivot.isactive' => 'boolean'
    ];
    public function nrdocumente() {
             return $this->hasMany('App\Nrdocumente');
    }
    public function identities() {
             return $this->hasMany('App\SocialIdentity');
    }

    public function companies() {
             return $this->belongsToMany(Company::class)->withTimestamps();
    }
    public function dianasoftmenuoptions() {
      

             return $this->belongsToMany(DianaSoftMenuOption::class,'dianasoftmenuoption_user','user_id','dianasoftmenuoption_id')
                         ->withPivot('isactive')
                         ->where('isactive',true)
                         // ->where("isdisabled",false)
                         ->withPivot('company_id')
                         ->orderBy('position2')
                         ->withTimestamps();
      
    }

    public function dianasoftmenuoptionsCompany()
    {
        $company_id=session('company_id');
        return $this->dianasoftmenuoptions()->where('dianasoftmenuoption_user.company_id',$company_id)->get();
        
    
    }
    public function alldianasoftmenuoptionsCompany()
    {
        $company_id=session('company_id');
        if(session("user_id")==1){
            return $this->belongsToMany(DianaSoftMenuOption::class,'dianasoftmenuoption_user','user_id','dianasoftmenuoption_id')
                    ->withPivot('isactive')->withPivot('company_id')
                    ->where('dianasoftmenuoption_user.company_id',$company_id)
                    ->orderBy('position2')
                    ->withTimestamps();
        }else{

        return $this->belongsToMany(DianaSoftMenuOption::class,'dianasoftmenuoption_user','user_id','dianasoftmenuoption_id')
                    ->withPivot('isactive')->withPivot('company_id')
                    ->where("isdisabled",false)
                    ->where('dianasoftmenuoption_user.company_id',$company_id)
                    ->orderBy('position2')
                    ->withTimestamps();
        }
    }
     public function gestiuni() {

             $company_id=session('company_id');
             if($company_id==null){
                $company_id=1;
             }
             return $this->belongsToMany(Gestiune::class,'gestiune_user')->withPivot('company_id')->withPivot('isactive')->withTimestamps();
    }
     public function gestiuniCompany()
    {
        $company_id=session('company_id');
        if($company_id==null){
                $company_id=1;
             }
        return $this->gestiuni()->where('isactive',true)->where('gestiune_user.company_id',$company_id)->get();
        
    
    }
     public function allgestiuniCompany()
    {
         $company_id=session('company_id');
         if($company_id==null){
                $company_id=1;
             }
             return $this->belongsToMany(Gestiune::class,'gestiune_user')->withPivot('company_id')->where('gestiune_user.company_id',$company_id)->withPivot('isactive')
                            ->orderBy("denumire","asc")->withTimestamps();
        
    
    }
    public function gestiuniPermiseCompany()
    {
        $company_id=session('company_id');
        if($company_id==null){
                $company_id=1;
             }
        return $this->gestiuni()
                        ->where('gestiune_user.company_id',$company_id)
                        ->where('gestiune_user.isactive',true)
                        ->orderBy('denumire','asc')
                        ->get();
        
    }

    public function permissions() {
             return $this->belongsToMany(Permission::class,'permission_user')->withPivot('isactive')->where('isactive',true)->withPivot('company_id')->withTimestamps();
    }
    public function permissionsCompany()
    {
        $company_id=session('company_id');
        return $this->permissions()->where('permission_user.company_id',$company_id)->get();
        
    }
    public function allpermissionsCompany()
    {
        $company_id=session('company_id');
        if (session("user_id")==1){
            return $this->belongsToMany(Permission::class,'permission_user')
                        ->withPivot('isactive')
                        ->withPivot('company_id')
                        ->with("dianasoftmenuoption")
                        ->where('permission_user.company_id',$company_id)
                        ->withTimestamps();
        }else{

            return $this->belongsToMany(Permission::class,'permission_user')
                        ->withPivot('isactive')
                        ->withPivot('company_id')
                        ->whereHas("dianasoftmenuoption",function($q){
                             $q->where("isdisabled",false);
                        })
                        ->with("dianasoftmenuoption")
                        ->orderBy('display_name','asc')
                        ->where('permission_user.company_id',$company_id)
                        ->withTimestamps();
       
        }
        
    }
    public function hasPermission($name,$company_id) {
             foreach ($this->permissions as $permission)
             {
             if($permission->name==$name && $permission->pivot->isactive && $permission->pivot->company_id==$company_id)
                {
                    return true;
                }
             }
             return false;
    }
    public function isOwner()
    {
       return $this->user_type=='owner';
    
    }
    public function hasPermissionToCompany($id)
    {
       foreach ($this->companies as $company)
             {
                if($company->id==$id)
                {
                    return true;
                }
             }
             return false;
    
    }
}
