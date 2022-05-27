<?php

namespace Hui\Xproject\Http\ViewComposers;

use Hui\Xproject\Entities\Synthetic\Statistic;
use Illuminate\View\View;

class StatisticsComposer{
  public function compose(View $view):void{
    $urls=[
      'list'=>route('statisticsList'),
      'download'=>route('statisticsDownload')
    ];
    $columns=[
      [
        'code'=>'row',
        'name'=>_ix('Row','Statistics'),
        'width'=>5
      ],
      [
        'code'=>'asinMarketplace',
        'name'=>_ix('To-Do ASIN & Marketplace','Statistics'),
        'width'=>10
      ],
      [
        'code'=>'seller',
        'name'=>_ix('Seller name & ID','Statistics'),
        'width'=>10
      ],
      [
        'code'=>'storefront',
        'name'=>_ix('Storefront','Statistics'),
        'width'=>10
      ],
      [
        'code'=>'freelancer',
        'name'=>_ix('Freelancer\'s name','Statistics'),
        'width'=>10
      ],
      [
        'code'=>'currency',
        'name'=>_ix('Project currency','Statistics'),
        'width'=>5,
        'notSortable'=>true
      ],
      [
        'code'=>'price',
        'name'=>_ix('Project price','Statistics'),
        'width'=>7.50
      ],
      [
        'code'=>'status',
        'name'=>_ix('Project status','Statistics'),
        'width'=>7.50
      ],
      [
        'code'=>'completion',
        'name'=>_ix('Project completion ID & URL','Statistics'),
        'width'=>15
      ],
      [
        'code'=>'dates',
        'name'=>_ix('Dates','Statistics'),
        'width'=>20
      ]
    ];

    if(user()->isManagerOrAdmin())
      $excludedStatuses=[];
    else
      $excludedStatuses=[
        Statistic::STATUS_UNASSIGNED,
        Statistic::STATUS_DELETED
      ];
    $statusNames=Statistic::statusNames();
    foreach($excludedStatuses as $status)
      unset($statusNames[$status]);

    $filters=[
      [
        'title'=>_ix('Date type','Statistics'),
        'name'=>'dateType',
        'radios'=>[
          'created'=>_ix('Project created date','Statistics'),
          'completionIdSubmitted'=>_ix('Completion ID submit date','Statistics'),
          'completed'=>_ix('Project completion date','Statistics')
        ],
        'value'=>'completed'
      ],
      [
        'title'=>_ix('Date','Statistics'),
        'name'=>'date',
        'type'=>'date',
        'range'=>true
      ],
      [
        'title'=>_ix('ASIN/ID','Statistics'),
        'name'=>'asinCode'
      ],
      [
        'title'=>_ix('Seller ID','Statistics'),
        'name'=>'sellerId'
      ],
      [
        'title'=>_ix('Seller name','Statistics'),
        'name'=>'sellerName'
      ],
      [
        'title'=>_ix('Freelancer name','Statistics'),
        'name'=>'freelancer'
      ],
      [
        'title'=>_ix('Status','Statistics'),
        'name'=>'status',
        'checkboxes'=>$statusNames,
        'value'=>[Statistic::STATUS_COMPLETED]
      ],
      [
        'title'=>_ix('Private','Statistics'),
        'name'=>'private',
        'options'=>[
          'yes'=>_ix('Yes, private project','Statistics'),
          'no'=>_ix('No, public project','Statistics')
        ]
      ]
    ];

    if(user()->isFreelancer())
      $filters=array_values(array_filter($filters,function(array $filter):bool{
        return $filter['name']!=='freelancer';
      }));

    $view->with(compact(
      'urls',
      'columns',
      'filters'
    ));
  }
}
