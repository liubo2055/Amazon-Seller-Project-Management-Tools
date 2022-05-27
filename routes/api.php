<?php

/*
 * To-Dos (notify URL)
 */
Route::post('/todos/notify-payment','TodosController@notifyPayment')
  ->name('todosNotifyPayment');
