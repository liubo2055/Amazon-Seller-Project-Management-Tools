<?php

namespace Hui\Xproject\Http\ViewComposers;

use Hui\Xproject\Entities\User;
use Hui\Xproject\Services\UserManager\UserManager;
use Illuminate\View\View;

class UsersComposer{
  public function compose(View $view):void{
    $user=user();

    /**
     * @var UserManager $userManager
     */
    $userManager=app(UserManager::class);

    $urls=[
      'list'=>route('usersList'),
      'new'=>route('usersLoadNew'),
      'upload'=>route('uploadPost')
    ];
    if($canImportUsers=$userManager->canImportUsers($user))
      $urls+=[
        'preview'=>route('usersPreviewImport'),
        'import'=>route('usersImport')
      ];
    $columns=[
      [
        'code'=>'email',
        'name'=>_ix('Email address','Users'),
        'width'=>15
      ],
      [
        'code'=>'name',
        'name'=>_ix('Name','Users'),
        'width'=>20
      ],
      [
        'code'=>'qq',
        'name'=>_ix('QQ','Users'),
        'width'=>10
      ],
      [
        'code'=>'phone',
        'name'=>_ix('Cell phone number','Users'),
        'width'=>10
      ],
      [
        'code'=>'alipayId',
        'name'=>_ix('Alipay ID','Users'),
        'width'=>10
      ],
      [
        'code'=>'role',
        'name'=>_ix('Role','Users'),
        'width'=>10
      ],
      [
        'code'=>'created',
        'name'=>_ix('Created at','Users'),
        'width'=>15
      ],
      [
        'code'=>'operations',
        'name'=>_ix('Operations','Users'),
        'width'=>10,
        'notSortable'=>true
      ]
    ];
    $filters=[
      [
        'title'=>_ix('Email or name','Users'),
        'name'=>'search'
      ]
    ];

    $editableRoles=$userManager->editableRolesFor($user);
    $editableRoles=array_combine($editableRoles,array_fill(0,count($editableRoles),true));
    $roles=array_intersect_key(User::roleNames(),$editableRoles);

    $timezones=array_combine(TIMEZONES,TIMEZONES);
    $timezones['Asia/Shanghai']='北京时间 Asia/Shanghai';
    $locales=[
      LOCALE_CN=>_ix('Chinese','Users'),
      LOCALE_EN=>_ix('English','Users')
    ];

    $showImportButton=$canImportUsers;
    $templateSheetUrl=asset('huideals-users.xlsx');

    if($onlyPrivateFreelancers=$userManager->canOnlyCreatePrivateFreelancers($user))
      $creatorUsers=collect([$user]);
    else
      $creatorUsers=User
        ::whereIn('role',$userManager->rolesForCreatorUsers())
        ->get();

    $creatorUsers=$creatorUsers->map(function(User $user):array{
      return [
        'id'=>$user->id,
        'name'=>$user->name,
        'description'=>$user->email
      ];
    })
      ->toArray();
    $freelancerRole=User::ROLE_FREELANCER;

    $view->with(compact(
      'urls',
      'columns',
      'filters',
      'roles',
      'timezones',
      'locales',
      'showImportButton',
      'templateSheetUrl',
      'creatorUsers',
      'onlyPrivateFreelancers',
      'freelancerRole'
    ));
  }
}
