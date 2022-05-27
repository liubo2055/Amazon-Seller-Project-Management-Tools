<?php

namespace Hui\Xproject\Entities\Synthetic;

use Hui\Xproject\Entities\Project;

/**
 * @property-read string        $code used to filter by ASIN/ID
 * @property-read string        $product_asin
 * @property-read string        $product_url
 * @property-read string        $marketplace
 * @property-read string|null   $seller_id
 * @property-read string|null   $seller_name
 * @property-read string|null   $storefront_url
 * @property-read string        $storefront_name
 * @property-read string[]|null $keywords
 * @property-read bool          $private
 * @property-read string|null   $freelancer
 */
class Statistic extends Project{
  protected $casts=[
    'project_price'=>'float',
    'original_project_price'=>'float',
    'keywords'=>'json'
  ];
}
