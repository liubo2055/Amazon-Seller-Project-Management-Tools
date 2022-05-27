<?php

namespace Hui\Xproject\Notifications;

use Hui\Xproject\Entities\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends ResetPasswordBase{
  public function toMail($notifiable):MailMessage{
    /**
     * @var User $notifiable
     */
    $url=sprintf('%s?email=%s',
      url(route('resetPassword',$this->token)),
      $notifiable->email);

    return (new MailMessage)->view('emails.forgotPassword',compact('url'));
  }
}
