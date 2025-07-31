<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegisterMail extends Mailable{
  use Queueable, SerializesModels;

  public $user;

  public function __construct($user)
  {
    $this->user = $user;
  }

  public function build()
  {
     return $this->markdown('emails.user_register')
                 ->subject(config('app.name') . ' Email validation');
  }
  
}