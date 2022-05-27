<?php

use Hui\Xproject\Entities\Todo;
use Hui\Xproject\Services\MarketplaceInformation\MarketplaceInformation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TodosSeeder extends Seeder{
  public function run():void{
    if(Todo::count())
      return;

    $now=now();

    /**
     * @var MarketplaceInformation $marketplaceInformation
     */
    $marketplaceInformation=app(MarketplaceInformation::class);

    $marketplaces=$marketplaceInformation->marketplaceCodes();

    for($i=0;$i<500;$i++){
      $marketplace=array_random($marketplaces);
      $asin=sprintf('B%s',strtoupper(str_random(9)));
      $sellerId=sprintf('A%s',strtoupper(str_random(13)));

      DB::table('todos')
        ->insert([
          'code'=>strtoupper(str_random(10)),
          'fba'=>mt_rand(false,true),
          'product_url'=>$marketplaceInformation->productUrl($marketplace,$asin),
          'product_asin'=>$asin,
          'marketplace'=>$marketplace,
          'product_price'=>mt_rand(1,100000)/100,
          'product_title'=>sprintf('Product %u',$i),
          'product_description'=>sprintf('Product %u description',$i),
          'seller_name'=>sprintf('Seller %u',$i),
          'storefront_url'=>$marketplaceInformation->storeUrl($marketplace,$sellerId,1),
          'storefront_name'=>sprintf('Storefront %u',$i),
          'seller_id'=>$sellerId,
          'notes'=>sprintf('Product %u notes',$i),
          'daily_limit'=>mt_rand(1,5),
          'created_at'=>$now->copy()
            ->subDays($i),
          'updated_at'=>$now
        ]);
    }
  }
}
