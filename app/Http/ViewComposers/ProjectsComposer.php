<?php

namespace Hui\Xproject\Http\ViewComposers;

use Hui\Xproject\Entities\Project;
use Hui\Xproject\Entities\Todo;
use Hui\Xproject\Services\TodoManager\TodoManager;
use Illuminate\View\View;

class ProjectsComposer{
  public function compose(View $view):void{
    $urls=[
      'list'=>route('projectsList'),
      'upload'=>route('uploadPost')
    ];
    $columns=[
      [
        'code'=>'rowAsinSeller',
        'name'=>_ix('Row, ASIN & Seller','Projects'),
        'width'=>10
      ],
      [
        'code'=>'todoTitleDescription',
        'name'=>_ix('To-Do title & Description','Projects'),
        'width'=>17.50
      ],
      [
        'code'=>'projectTitleDescription',
        'name'=>_ix('Project title & Description','Projects'),
        'width'=>17.50
      ],
      [
        'code'=>'projectPrice',
        'name'=>_ix('Project price','Projects'),
        'width'=>7.50
      ],
      [
        'code'=>'imageMedia',
        'name'=>_ix('Image & Media','Projects'),
        'width'=>7.50,
        'notSortable'=>true
      ],
      [
        'code'=>'status',
        'name'=>_ix('Status','Projects'),
        'width'=>7.50
      ],
      [
        'code'=>'completion',
        'name'=>_ix('Completion ID & Freelancer','Projects'),
        'width'=>15
      ],
      [
        'code'=>'dates',
        'name'=>_ix('Dates','Projects'),
        'width'=>10
      ],
      [
        'code'=>'operations',
        'name'=>_ix('Operations','Projects'),
        'width'=>7.50,
        'notSortable'=>true
      ]
    ];

    /**
     * @var TodoManager $todoManager
     */
    $todoManager=app(TodoManager::class);
    $todos=$todoManager->restrictQueryForUser(Todo::query(),user());
    $filters=[
      [
        'title'=>_ix('To-Do','Projects'),
        'name'=>'todo',
        'options'=>$todos->get()
          ->mapWithKeys(function(Todo $todo):array{
            return [
              $todo->id=>$todo->code
            ];
          }),
        'value'=>$view->offsetExists('todoId')?$view->offsetGet('todoId'):null
      ],
      [
        'title'=>_ix('Project description','Projects'),
        'name'=>'description',
        'options'=>[
          'yes'=>_ix('Yes, employers must write review contents','Projects'),
          'no'=>_ix('No, freelancers will write review contents','Projects')
        ]
      ],
      [
        'title'=>_ix('ASIN','Projects'),
        'name'=>'asin'
      ],
      [
        'title'=>_ix('Seller ID/storefront name','Projects'),
        'name'=>'sellerIdStorefrontName'
      ],
      [
        'title'=>_ix('Seller name','Projects'),
        'name'=>'sellerName'
      ],
      [
        'title'=>_ix('Freelancer name','Projects'),
        'name'=>'freelancer'
      ],
      [
        'title'=>_ix('Creation date','Projects'),
        'name'=>'date',
        'type'=>'date',
        'range'=>true
      ],
      [
        'title'=>_ix('Status','Projects'),
        'name'=>'status',
        'checkboxes'=>Project::statusNames(),
        'value'=>array_diff(Project::STATUSES,[
          Project::STATUS_COMPLETED,
          Project::STATUS_FAILED
        ])
      ],
      [
        'title'=>_ix('Private','Projects'),
        'name'=>'private',
        'options'=>[
          'yes'=>_ix('Yes, private project','Projects'),
          'no'=>_ix('No, public project','Projects')
        ]
      ]
    ];

    if(!user()->isManagerOrAdmin()){
      $columns=array_values(array_filter($columns,function(array $column):bool{
        return $column['code']!=='freelancer';
      }));
      $filters=array_values(array_filter($filters,function(array $filter):bool{
        return $filter['name']!=='freelancer';
      }));

      if(user()->isFreelancer())
        $filters=array_values(array_filter($filters,function(array $filter):bool{
          return $filter['name']!=='sellerName';
        }));
    }

    $similarProjectsColumns=array_filter($columns,function(array $column):bool{
      return !in_array($column['code'],[
        'projectTitleDescription',
        'imageMedia',
        'operations'
      ]);
    });
    $widthIncrease=30/count($similarProjectsColumns);
    $similarProjectsColumns=array_map(function(array $column) use ($widthIncrease):array{
      $column['width']+=$widthIncrease;

      return $column;
    },$similarProjectsColumns);

    $view->with(compact(
      'urls',
      'columns',
      'filters',
      'similarProjectsColumns'
    ));
  }
}
