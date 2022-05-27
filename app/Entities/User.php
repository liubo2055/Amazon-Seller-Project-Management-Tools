<?php

namespace Hui\Xproject\Entities;

use Carbon\Carbon;
use Hui\Xproject\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

/**
 * @property-read int     $id
 * @property int|null     $created_by_user_id
 * @property string       $email
 * @property string       $password
 * @property string       $role
 * @property string       $name
 * @property string|null  $qq
 * @property string|null  $wechat_id
 * @property string       $locale
 * @property string       $timezone
 * @property string|null  $phone
 * @property string|null  $company_name
 * @property string|null  $company_url
 * @property string|null  $alipay_id
 * @property string|null  $image_path
 * @property string|null  $image_url
 * @property string|null  $notes
 * @property mixed[]|null $storefronts
 * @property Carbon|null  $blocked_at
 * @property Carbon|null  $kicked_at
 * @property int|null     $apps_io_user_id
 * @property string|null  $register_date
 * @property string|null  $remember_token
 * @property Carbon       $created_at
 * @property Carbon       $updated_at
 * @property Carbon|null  $deleted_at
 * @property User|null    $creatorUser
 */
class User extends Authenticatable{
  use SoftDeletes,
    Notifiable;

  const ROLES=[
    self::ROLE_ADMIN,
    self::ROLE_MANAGER,
    self::ROLE_FREELANCER,
    self::ROLE_EMPLOYER,
    self::ROLE_FREE_EMPLOYER
  ];
  const ROLE_ADMIN='admin';
  const ROLE_EMPLOYER='employer';
  const ROLE_FREE_EMPLOYER='freeEmployer';
  const ROLE_FREELANCER='freelancer';
  const ROLE_MANAGER='manager';

  public static function roleNames():array{
    return [
      static::ROLE_ADMIN=>_ix('Administrator','User'),
      static::ROLE_MANAGER=>_ix('Manager','User'),
      static::ROLE_FREELANCER=>_ix('Freelancer','User'),
      static::ROLE_EMPLOYER=>_ix('Employer','User'),
      static::ROLE_FREE_EMPLOYER=>_ix('Free employer','User')
    ];
  }

  public static function findByEmail(string $email,bool $ignoreCase):?User{
    if(!$ignoreCase)
      return static
        ::where(compact('email'))
        ->first();
    else
      return static
        ::where(DB::raw('lower(email)'),mb_strtolower($email))
        ->first();
  }

  public static function findByRole(string $role):Collection{
    return static::findByRoles([$role]);
  }

  public static function findByRoles(array $roles):Collection{
    return static
      ::whereIn('role',$roles)
      ->get();
  }

  public function creatorUser():BelongsTo{
    return $this->belongsTo(static::class,'created_by_user_id');
  }

  public function isAdmin():bool{
    return $this->role===static::ROLE_ADMIN;
  }

  public function isManager():bool{
    return $this->role===static::ROLE_MANAGER;
  }

  public function isManagerOrAdmin():bool{
    return $this->isAdmin()||$this->isManager();
  }

  public function isFreelancer():bool{
    return $this->role===static::ROLE_FREELANCER;
  }

  public function isEmployer():bool{
    return $this->role===static::ROLE_EMPLOYER||$this->role===static::ROLE_FREE_EMPLOYER;
  }

  public function roleName():string{
    return static::roleNames()[$this->role];
  }

  public function isComplete():bool{
    foreach([
      $this->name,
      $this->email,
      $this->qq,
      $this->phone,
      $this->timezone,
      $this->locale
    ] as $field)
      if((string)$field==='')
        return false;

    if($this->isEmployer()){
      if(!$this->storefronts)
        return false;

      $storefront=$this->storefronts[0]+[
          'url'=>null,
          'names'=>[]
        ];

      if((string)$storefront['url']===''||!$storefront['names'])
        return false;
    }

    return true;
  }

  public function isKicked(?int $loginTime):bool{
    if($this->kicked_at===null)
      return false;
    // If login time is not available, return true
    else if(!$loginTime)
      return true;
    // If the user was kicked after login was registered, return true
    else
      return $this->kicked_at->getTimestamp()>$loginTime;
  }

  public function isBlocked():bool{
    return $this->blocked_at!==null;
  }

  public function isPrivateFreelancer():bool{
    return $this->isFreelancer()&&$this->created_by_user_id;
  }

  public function isCreatedBy(User $user):bool{
    return $this->isPrivateFreelancer()&&$this->created_by_user_id===$user->id;
  }

  public function sendPasswordResetNotification($token):void{
    $this->notify(new ResetPassword($token));
  }

  protected $dates=[
    'kicked_at',
    'blocked_at',
    'deleted_at'
  ];

  protected $casts=[
    'storefronts'=>'json'
  ];

  protected $visible=[
    'id',
    'email',
    'role',
    'name',
    'qq',
    'wechat_id',
    'locale',
    'timezone',
    'phone',
    'company_name',
    'company_url',
    'alipay_id',
    'image_url',
    'storefronts'
  ];
}
