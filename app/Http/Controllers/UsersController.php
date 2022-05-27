<?php

namespace Hui\Xproject\Http\Controllers;

use Hui\Xproject\Entities\User;
use Hui\Xproject\Http\Controllers\Traits\CloudUploadTrait;
use Hui\Xproject\Http\Controllers\Traits\ListTrait;
use Hui\Xproject\Services\UploadManager\UploadManager;
use Hui\Xproject\Services\UserImporter\ImportedUser;
use Hui\Xproject\Services\UserImporter\UserImporter;
use Hui\Xproject\Services\UserManager\UserManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UsersController extends Controller{
  use ListTrait,
    CloudUploadTrait;

  public function __construct(UploadManager $uploadManager,UserManager $userManager){
    $this->uploadManager=$uploadManager;
    $this->userManager=$userManager;
  }

  public function index():View{
    return view('users');
  }

  public function list(Request $request):array{
    $orderCallback=function(string $order):string{
      switch($order){
        case 'created':
          return 'created_at';
        default:
          return snake_case($order);
      }
    };

    $rowsCallback=function(string $orderColumn,bool $ascOrder,int $page,int $size,array $filters):array{
      $query=User
        ::orderBy($orderColumn,$ascOrder?'ASC':'DESC')
        ->offset(($page-1)*$size)
        ->limit($size);
      $this->userManager->restrictQueryForUser($query,user());
      $this->filter($query,$filters);
      $users=$query->get();

      $rows=[];
      /**
       * @var User $user
       */
      foreach($users as $user){
        $operations=[
          'edit'=>[
            'url'=>route('usersLoad',$user->id)
          ]
        ];
        if(!$user->isBlocked())
          $operations['block']=[
            'url'=>route('usersBlock',$user->id)
          ];
        else
          $operations['unblock']=[
            'url'=>route('usersUnblock',$user->id)
          ];

        $rows[]=[
          'email'=>$user->email,
          'name'=>[
            'name'=>$user->name,
            'private'=>$user->isPrivateFreelancer(),
            'creatorUser'=>$user->isPrivateFreelancer()?$user->creatorUser->name:null
          ],
          'qq'=>$user->qq,
          'phone'=>$user->phone,
          'alipayId'=>$user->alipay_id,
          'role'=>$user->roleName(),
          'created'=>$user->created_at->format('Y-m-d H:i:s'),
          'operations'=>$operations,
          'meta'=>[
            'id'=>$user->id
          ]
        ];
      }

      return $rows;
    };

    $itemsCallback=function(array $filters):int{
      $count=User::query();
      if(!user()->isAdmin())
        $count->where('role',User::ROLE_FREELANCER);
      $this->filter($count,$filters);

      return $count->count();
    };

    return $this->makeListResponse($request,$orderCallback,$rowsCallback,$itemsCallback);
  }

  public function load(User $user):array{
    if(!$user->exists&&$this->userManager->canOnlyCreatePrivateFreelancers(user())){
      $user->role=user::ROLE_FREELANCER;
      $user->created_by_user_id=user()->id;
    }

    $data=$user->toArray()+[
        'registerDate'=>$user->register_date,
        'notes'=>$user->notes,
        'creatorUser'=>$user->created_by_user_id,
        'url'=>$user->exists?route('usersSave',$user->id):route('usersSaveNew')
      ];
    $data=arrayCamelCase($data);

    return $data;
  }

  public function save(User $user,Request $request):void{
    $data=$request->all();
    // As required by the "confirmed" rule
    if(!empty($data['passwordConfirmation']))
      $data['password_confirmation']=$data['passwordConfirmation'];

    $currentUser=user();

    validator($data,[
      // ID will be 0 if the user is new (this is not an issue)
      'email'=>sprintf('required|email|max:255|unique:users,email,%u',$user->id),
      'password'=>sprintf('%s|min:8|max:16|confirmed',$user->exists?'nullable':'required'),
      'role'=>sprintf('required|in:%s',implode(',',$this->userManager->editableRolesFor($currentUser))),
      'name'=>'required|max:255',
      'phone'=>'required|max:255',
      'timezone'=>sprintf('required|in:%s',implode(',',TIMEZONES)),
      'locale'=>sprintf('required|in:%s',implode(',',LOCALES)),
      'qq'=>'required|max:255',
      'wechatId'=>'nullable|max:255',
      'alipayId'=>'nullable|max:255',
      'companyName'=>'nullable|max:255',
      'companyUrl'=>'nullable|url|max:255'
    ])->validate();

    $user->email=$request->input('email');
    if($request->input('password'))
      $user->password=bcrypt($request->input('password'));
    $user->role=$request->input('role');
    $user->name=$request->input('name');
    $user->phone=$request->input('phone');
    $user->timezone=$request->input('timezone');
    $user->locale=$request->input('locale');
    $user->qq=$request->input('qq');
    $user->wechat_id=$request->input('wechatId');
    $user->alipay_id=$request->input('alipayId');
    $user->company_name=$request->input('companyName');
    $user->company_url=$request->input('companyUrl');
    $user->notes=$request->input('notes');

    if($user->isFreelancer()){
      if(!$user->exists&&$this->userManager->canOnlyCreatePrivateFreelancers($currentUser))
        $user->created_by_user_id=$currentUser->id;
      else if(!$this->userManager->canOnlyCreatePrivateFreelancers($currentUser))
        $user->created_by_user_id=$request->input('creatorUser');

      if($user->creatorUser&&!in_array($user->creatorUser->role,$this->userManager->rolesForCreatorUsers()))
        throw new HttpException(400,'Wrong role for creator user');
    }
    else
      $user->created_by_user_id=null;

    $user->save();
  }

  public function block(User $user):void{
    $user->blocked_at=now();
    $user->save();
  }

  public function unblock(User $user):void{
    $user->blocked_at=null;
    $user->save();
  }

  public function previewImport(Request $request,UserImporter $userImporter):array{
    $upload=$this->uploadManager->loadUpload($request->input('code'));
    $importedUsers=$userImporter->extract($upload);

    $rows=array_map(function(ImportedUser $importedUser):array{
      $user=$importedUser->user;

      return [
        'row'=>$importedUser->row,
        'imported'=>$importedUser->imported,
        'user'=>$user->toArray()+[
            'notes'=>$user->notes,
            'registerDate'=>$user->register_date
          ],
        'error'=>$importedUser->error
      ];
    },$importedUsers);
    $rows=arrayCamelCase($rows);

    return $rows;
  }

  public function import(Request $request,UserImporter $userImporter):array{
    $code=$request->input('code');

    $upload=$this->uploadManager->loadUpload($code);
    $importedUsers=$userImporter->extract($upload);

    DB::beginTransaction();
    DB::statement('LOCK users IN ACCESS EXCLUSIVE MODE');

    $resultObject=$userImporter->import($importedUsers);

    DB::commit();

    $result=[];
    $result[]=sprintf(_ix('%u users have been imported','Users'),$resultObject->imported);
    if($resultObject->errors)
      $result[]=sprintf(_ix('%u users contained errors','Users'),$resultObject->errors);
    if($resultObject->existing)
      $result[]=sprintf(_ix('%u users already existed','Users'),$resultObject->existing);
    $result=implode(', ',$result);

    $this->deleteTempUploads([$code]);

    return compact('result');
  }

  public function delete(User $user):void{
    $user->delete();
  }

  public function restore(User $user):void{
    $user->restore();
  }

  private function filter(Builder $query,array $filters):void{
    if(isset($filters['search'])){
      $pattern=sprintf('%%%s%%',mb_strtolower($filters['search']));
      $query->whereRaw('(lower(email) LIKE ? OR lower(name) LIKE ?)',[
        $pattern,
        $pattern
      ]);
    }
  }

  private $userManager;
}
