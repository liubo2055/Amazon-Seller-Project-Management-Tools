<?php

return [
  'fees'=>[
    'todos'=>env('TODOS_FEE',60),
  ],
  'exchangeRates'=>[
    'USD'=>7.2,
    'CAD'=>6,
    'EUR'=>8.5,
    'GBP'=>9,
    'JPY'=>0.065
  ],
  'test_payments'=>env('TEST_PAYMENTS',false),
  'alipay'=>[
    'seller_email'=>env('ALIPAY_SELLER_EMAIL'),
    'partner'=>env('ALIPAY_PARTNER'),
    'key'=>env('ALIPAY_KEY')
  ]
];
