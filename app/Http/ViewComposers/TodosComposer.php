<?php

namespace Hui\Xproject\Http\ViewComposers;

use Hui\Xproject\Entities\Todo;
use Hui\Xproject\Services\MarketplaceInformation\MarketplaceInformation;
use Illuminate\View\View;

class TodosComposer{
  public function compose(View $view):void{
    $user=user();

    $urls=[
      'list'=>route('todosList'),
      'new'=>$user->can('create',Todo::class)?route('todosLoadNew'):null,
      'newPrivate'=>$user->can('createPrivate',Todo::class)?route('todosLoadNewPrivate'):null,
      'guess'=>route('todosGuess')
    ];
    $columns=[
      [
        'code'=>'code',
        'name'=>_ix('ID & ASIN','To-Dos'),
        'width'=>7.50
      ],
      [
        'code'=>'price',
        'name'=>_ix('Product price','To-Dos'),
        'width'=>10
      ],
      [
        'code'=>'title',
        'name'=>_ix('Product title & Description','To-Dos'),
        'width'=>12.50
      ],
      [
        'code'=>'seller',
        'name'=>_ix('Seller name & ID','To-Dos'),
        'width'=>10
      ],
      [
        'code'=>'notesKeywords',
        'name'=>_ix('Notes & Keywords','To-Dos'),
        'width'=>15
      ],
      [
        'code'=>'status',
        'name'=>_ix('To-Do status','To-Dos'),
        'width'=>10,
        'notSortable'=>true
      ],
      [
        'code'=>'progress',
        'name'=>_ix('To-Do progress','To-Dos'),
        'width'=>20,
        'notSortable'=>true
      ],
      [
        'code'=>'created',
        'name'=>_ix('Created at','To-Dos'),
        'width'=>7.50
      ],
      [
        'code'=>'operations',
        'name'=>_ix('Operations','To-Dos'),
        'width'=>7.50,
        'notSortable'=>true
      ]
    ];

    $statuses=Todo::statusNames();
    $initialStatuses=[
      Todo::STATUS_UNASSIGNED,
      Todo::STATUS_ACCEPTED,
      Todo::STATUS_EMPLOYER_UNPAID,
      Todo::STATUS_EMPLOYER_UNCONFIRMED
    ];
    if(!$user->isManagerOrAdmin()){
      unset($statuses[Todo::STATUS_DELETED]);

      if($user->isFreelancer()){
        unset($statuses[Todo::STATUS_EMPLOYER_UNPAID]);
        unset($statuses[Todo::STATUS_EMPLOYER_UNCONFIRMED]);
        $initialStatuses=[
          Todo::STATUS_UNASSIGNED,
          Todo::STATUS_ACCEPTED,
        ];
      }
    }
    $filters=[
      [
        'title'=>_ix('Statuses','To-Dos'),
        'name'=>'statuses',
        'checkboxes'=>$statuses,
        'value'=>$initialStatuses
      ],
      [
        'title'=>_ix('ASIN/ID','To-Dos'),
        'name'=>'asinCode'
      ],
      [
        'title'=>_ix('Seller ID/storefront name','To-Dos'),
        'name'=>'sellerIdStorefrontName'
      ],
      [
        'title'=>_ix('Seller name','To-Dos'),
        'name'=>'sellerName'
      ],
      [
        'title'=>_ix('Freelancer name','To-Dos'),
        'name'=>'freelancer'
      ],
      [
        'title'=>_ix('Creation date','To-Dos'),
        'name'=>'date',
        'type'=>'date',
        'range'=>true
      ],
      [
        'title'=>_ix('Private','To-Dos'),
        'name'=>'private',
        'options'=>[
          'yes'=>_ix('Private to-do','To-Dos'),
          'no'=>_ix('Public to-do','To-Dos')
        ]
      ]
    ];

    if(!$user->isManagerOrAdmin())
      $filters=array_values(array_filter($filters,function(array $filter):bool{
        return $filter['name']!=='freelancer';
      }));

    /**
     * @var MarketplaceInformation $marketplaceInformation
     */
    $marketplaceInformation=app(MarketplaceInformation::class);
    $marketplaces=$marketplaceInformation->availableMarketplaces();

    $view->with(compact(
      'urls',
      'columns',
      'filters',
      'marketplaces'
    ));
  }
}
