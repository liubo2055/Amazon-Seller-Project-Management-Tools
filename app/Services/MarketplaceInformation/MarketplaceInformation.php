<?php

namespace Hui\Xproject\Services\MarketplaceInformation;

class MarketplaceInformation{
  public function marketplaceCodes():array{
    return array_keys($this->marketplaces());
  }

  public function marketplaces():array{
    return [
      'CA'=>_ix('Canada','Marketplace information'),
      'US'=>_ix('USA','Marketplace information'),
      'MX'=>_ix('Mexico','Marketplace information'),
      'DE'=>_ix('Germany','Marketplace information'),
      'ES'=>_ix('Spain','Marketplace information'),
      'FR'=>_ix('France','Marketplace information'),
      'IT'=>_ix('Italy','Marketplace information'),
      'UK'=>_ix('UK','Marketplace information'),
      'JP'=>_ix('Japan','Marketplace information'),
      'IN'=>_ix('India','Marketplace information'),
      'CN'=>_ix('China','Marketplace information')
    ];
  }

  public function availableMarketplaces():array{
    $marketplaces=$this->marketplaces();
    unset($marketplaces['IN']);
    unset($marketplaces['CN']);
    unset($marketplaces['MX']);

    return $marketplaces;
  }

  public function decimalMarkPoint(string $marketplace):bool{
    switch($marketplace){
      case 'ES':
      case 'FR':
      case 'DE':
      case 'IT':
        return false;
      default:
        return true;
    }
  }

  public function id(string $marketplace):string{
    switch($marketplace){
      case 'CA':
        return 'A2EUQ1WTGCTBG2';
      case 'US':
        return 'ATVPDKIKX0DER';
      case 'MX':
        return 'A1AM78C64UM0Y8';
      case 'DE':
        return 'A1PA6795UKMFR9';
      case 'ES':
        return 'A1RKKUPIHCS9HS';
      case 'FR':
        return 'A13V1IB3VIYZZH';
      case 'IT':
        return 'APJ6JRA9NG5V4';
      case 'UK':
        return 'A1F83G8C2ARO7P';
      case 'IN':
        return 'A21TJRUUN4KGV';
      case 'JP':
        return 'A1VC38T7YXB528';
      case 'CN':
        return 'AAHKV2X7AFYLW';
      default:
        throw new MarketplaceInformationException(sprintf('Wrong marketplace: "%s"',$marketplace));
    }
  }

  public function url(string $marketplace,string $url='',string $subdomain='www'):string{
    $tlds=$this->tlds();
    if(!isset($tlds[$marketplace]))
      throw new MarketplaceInformationException(sprintf('Wrong marketplace: "%s"',$marketplace));

    $tld=$tlds[$marketplace];

    if((string)$url!==''&&$url[0]!=='/')
      $url='/'.$url;

    return sprintf('https://%s.amazon%s%s',
      $subdomain,
      $tld,
      $url);
  }

  public function searchPageUrl(string $marketplace,string $term,int $page):string{
    if($page===1){
      $url='s?';
      $params=[
        'url'=>'search-alias=aps',
        'field-keywords'=>$term
      ];
    }
    else{
      $url=sprintf('s/ref=sr_pg_%u?',$page);
      $params=[
        'sf'=>'col',
        'rh'=>sprintf('i:aps,k=%s',$term),
        'page'=>$page,
        'keywords'=>$term,
        'ie'=>'UTF8',
        'qid'=>time(),
        'spIA'=>''
      ];
    }

    return $this->url($marketplace,$url).http_build_query($params);
  }

  public function autocompleteUrl(string $marketplace,string $term,string $requestId,string $sessionId):?string{
    if($marketplace==='IN')
      return null;

    $params=[
      'method'=>'completion',
      'mkt'=>$this->marketId($marketplace),
      'r'=>$requestId,
      's'=>$sessionId,
      'c'=>'',
      'p'=>'Gateway',
      'l'=>$this->locale($marketplace),
      'sv'=>'desktop',
      'client'=>'amazon-search-ui',
      'x'=>'String',
      'search-alias'=>'aps',
      'q'=>$term,
      'qs'=>'',
      'cf'=>'1',
      'fb'=>'1',
      'sc'=>'1'
    ];

    switch($marketplace){
      case 'CA':
      case 'MX':
        $autocompleteMarketplace='US';
        break;
      case 'DE':
      case 'ES':
      case 'FR':
      case 'IT':
        $autocompleteMarketplace='UK';
        break;
      default:
        $autocompleteMarketplace=$marketplace;
    }

    return $this->url($autocompleteMarketplace,'search/complete?','completion').http_build_query($params);
  }

  public function productUrl(string $marketplace,string $asin):string{
    return $this->url($marketplace,sprintf('dp/%s',$asin));
  }

  public function productOffersUrl(string $marketplace,string $asin,int $page=1):string{
    $url=$this->url($marketplace,sprintf('gp/offer-listing/%s?condition=new',$asin));
    if($page>1)
      $url.=sprintf('&startIndex=%u',10*($page-1));

    return $url;
  }

  public function productReviewsUrl(string $marketplace,string $asin,bool $critical,int $page=1):string{
    $params=[
      'ie'=>'UTF8',
      'reviewerType'=>'all_reviews',
      'sortBy'=>'recent',
      'pageNumber'=>$page,
      'filterByStar'=>$critical?'critical':'all'
    ];

    return $this->url($marketplace,sprintf('product-reviews/%s/ref=cm_cr_arp_d_viewopt_sr?',$asin).http_build_query($params));
  }

  public function storeUrl(string $marketplace,string $sellerId,int $page):string{
    if($page===1){
      $url='s?';
      $params=[
        'marketplaceID'=>$this->id($marketplace),
        'me'=>$sellerId,
        'merchant'=>$sellerId
      ];
    }
    else{
      $url=sprintf('s/ref=sr_pg_%u?',$page);
      $params=[
        'me'=>$sellerId,
        'rh'=>'i:merchant-items',
        'page'=>$page,
        'ie'=>'UTF8',
        'qid'=>time()
      ];
    }

    return $this->url($marketplace,$url).http_build_query($params);
  }

  public function storeProfileUrl(string $marketplace,string $sellerId):string{
    $params=[
      '_encoding'=>'UTF8',
      'asin'=>'',
      'isAmazonFulfilled'=>'',
      'isCBA'=>'',
      'marketplaceID'=>$this->id($marketplace),
      'orderID'=>'',
      'seller'=>$sellerId,
      'tab'=>'',
      'vasStoreID'=>''
    ];

    return $this->url($marketplace,'sp?').http_build_query($params);
  }

  public function language(string $marketplace):string{
    return substr($this->locale($marketplace),0,2);
  }

  public function locale(string $marketplace):string{
    switch($marketplace){
      case 'CA':
        return 'en_CA';
      case 'US':
        return 'en_US';
      case 'MX':
        return 'es_MX';
      case 'DE':
        return 'de_DE';
      case 'ES':
        return 'es_ES';
      case 'FR':
        return 'fr_FR';
      case 'IT':
        return 'it_IT';
      case 'UK':
        return 'en_GB';
      case 'JP':
        return 'ja_JP';
      case 'CN':
        return 'zh_CN';
      // Unknown for IN (but not used)
      default:
        throw new MarketplaceInformationException(sprintf('Wrong marketplace: "%s"',$marketplace));
    }
  }

  public function valid(string $marketplace):bool{
    return in_array($marketplace,$this->marketplaceCodes());
  }

  public function name(string $marketplace):string{
    $marketplaces=$this->marketplaces();

    if(isset($marketplaces[$marketplace]))
      return $marketplaces[$marketplace];
    else
      throw new MarketplaceInformationException(sprintf('Wrong marketplace: "%s"',$marketplace));
  }

  public function nodeIdUrl(string $marketplace,int $id):string{
    $params=[
      'node'=>$id
    ];

    return $this->url($marketplace,'b?').http_build_query($params);
  }

  public function userProfileUrl(string $marketplace,string $id):string{
    return $this->url($marketplace,sprintf('gp/pdp/profile/%s/ref=cm_cr_arp_d_pdp?ie=UTF8',$id));
  }

  public function reviewCommentsUrlAndPost(string $marketplace,string $asin,string $threadId,int $offset,int $size,int $index):array{
    $url=$this->url($marketplace,'ss/customer-reviews/ajax/comment/get/ref=cm_cr_getr_d_cmt_opn');
    $post=[
      'threadId'=>$threadId,
      'sortCommentsBy'=>'newest',
      'offset'=>$offset,
      'count'=>$size,
      'pageIteration'=>$index,
      'asin'=>$asin,
      'scope'=>sprintf('reviewsAjax%u',$index)
    ];

    return [
      $url,
      $post
    ];
  }

  public function reviewUrl(string $marketplace,string $asin,string $id):string{
    return $this->url($marketplace,sprintf('gp/customer-reviews/%s/ref=cm_cr_getr_d_rvw_ttl?ie=UTF8&ASIN=%s',
      $id,
      $asin));
  }

  public function tlds():array{
    return [
      'CA'=>'.ca',
      'US'=>'.com',
      'MX'=>'.com.mx',
      'DE'=>'.de',
      'ES'=>'.es',
      'FR'=>'.fr',
      'IT'=>'.it',
      'UK'=>'.co.uk',
      'IN'=>'.in',
      'JP'=>'.co.jp',
      'CN'=>'.cn'
    ];
  }

  public function guessAsinAndMarketplace(string $url):array{
    $path=parse_url($url,PHP_URL_PATH);
    if($path&&preg_match('/\/(B[\dA-Z]{9}|\d{9}(?:X|\d))/',$path,$asin))
      $asin=$asin[1];
    else
      $asin=null;

    $marketplacesForTlds=array_flip($this->tlds());

    $marketplace=null;
    if(preg_match('|https?://.*?amazon+(\.[a-z.]+)/|',$url,$tld)){
      $tld=$tld[1];
      if(isset($marketplacesForTlds[$tld]))
        $marketplace=$marketplacesForTlds[$tld];
    }

    return [
      $asin,
      $marketplace
    ];
  }

  public function guessMerchantAndMarketplace(string $url):array{
    $merchantParameters=[
      'merchant',
      'seller',
      'me'
    ];

    $sellerId=null;
    $query=parse_url($url,PHP_URL_QUERY);
    if($query)
      foreach(explode('&',$query) as $param){
        [
          $key,
          $value
        ]=explode('=',$param)+[
          '',
          ''
        ];

        if(in_array($key,$merchantParameters)){
          $sellerId=$value;
          break;
        }
      }

    $marketplacesForTlds=array_flip($this->tlds());

    $marketplace=null;
    if(preg_match('|https?://.*?amazon+(\.[a-z.]+)/|',$url,$tld)){
      $tld=$tld[1];
      if(isset($marketplacesForTlds[$tld]))
        $marketplace=$marketplacesForTlds[$tld];
    }

    return [
      $sellerId,
      $marketplace
    ];
  }

  public function region(string $marketplace):string{
    switch($marketplace){
      case 'CA':
      case 'US':
      case 'MX':
        return 'NA';
      case 'DE':
      case 'ES':
      case 'FR':
      case 'IT':
      case 'UK':
        return 'EU';
      case 'IN':
        return 'IN';
      case 'JP':
        return 'FE';
      case 'CN':
        return 'CN';
      default:
        throw new MarketplaceInformationException(sprintf('Wrong marketplace: "%s"',$marketplace));
    }
  }

  public function capitalCity(string $marketplace):string{
    switch($marketplace){
      case 'CA':
        return _ix('Ottawa','Marketplace information');
      case 'US':
        return _ix('Washington, D.C.','Marketplace information');
      case 'MX':
        return _ix('Mexico City','Marketplace information');
      case 'DE':
        return _ix('Berlin','Marketplace information');
      case 'ES':
        return _ix('Madrid','Marketplace information');
      case 'FR':
        return _ix('Paris','Marketplace information');
      case 'IT':
        return _ix('Rome','Marketplace information');
      case 'UK':
        return _ix('London','Marketplace information');
      case 'IN':
        return _ix('New Delhi','Marketplace information');
      case 'JP':
        return _ix('Tokyo','Marketplace information');
      case 'CN':
        return _ix('Beijing','Marketplace information');
      default:
        throw new MarketplaceInformationException(sprintf('Wrong marketplace: "%s"',$marketplace));
    }
  }

  public function timezone(string $marketplace):string{
    switch($marketplace){
      case 'CA':
        return 'Canada/Eastern';
      case 'US':
        return 'US/Eastern';
      case 'MX':
        return 'America/Mexico_City';
      case 'DE':
        return 'Europe/Berlin';
      case 'ES':
        return 'Europe/Madrid';
      case 'FR':
        return 'Europe/Paris';
      case 'IT':
        return 'Europe/Rome';
      case 'UK':
        return 'Europe/London';
      case 'IN':
        return 'Asia/Kolkata';
      case 'JP':
        return 'Asia/Tokyo';
      case 'CN':
        return 'Asia/Shanghai';
      default:
        throw new MarketplaceInformationException(sprintf('Wrong marketplace: "%s"',$marketplace));
    }
  }

  public function currency(string $marketplace):string{
    switch($marketplace){
      case 'CA':
        return 'CAD';
      case 'US':
        return 'USD';
      case 'MX':
        return 'MXN';
      case 'DE':
      case 'ES':
      case 'FR':
      case 'IT':
        return 'EUR';
      case 'UK':
        return 'GBP';
      case 'JP':
        return 'JPY';
      case 'IN':
        return 'INR';
      case 'CN':
        return 'CNY';
      default:
        throw new MarketplaceInformationException(sprintf('Wrong marketplace: "%s"',$marketplace));
    }
  }

  public function addTag(string $url,string $subscriptionId,string $tag,string $keyword):string{
    $components=parse_url($url)+[
        'scheme'=>'',
        'host'=>'',
        'path'=>'',
        'query'=>''
      ];

    parse_str($components['query'],$params);
    $params['SubscriptionId']=$subscriptionId;
    $params['tag']=$tag;
    if($keyword!=='')
      $params['keywords']=$keyword;
    $params['qid']=time();

    return sprintf('%s://%s%s?%s',
      $components['scheme'],
      $components['host'],
      $components['path'],
      http_build_query($params)
    );
  }

  private function marketId(string $marketplace):int{
    switch($marketplace){
      case 'CA':
        return 7;
      case 'US':
        return 1;
      case 'MX':
        return 771770;
      case 'DE':
        return 4;
      case 'ES':
        return 44551;
      case 'FR':
        return 5;
      case 'IT':
        return 35691;
      case 'UK':
        return 3;
      case 'JP':
        return 6;
      case 'CN':
        return 3240;
      // Unknown for IN (but not used)
      default:
        throw new MarketplaceInformationException(sprintf('Wrong marketplace: "%s"',$marketplace));
    }
  }
}
