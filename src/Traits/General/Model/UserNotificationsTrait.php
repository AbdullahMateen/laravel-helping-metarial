<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\General\Model;

trait UserNotificationsTrait
{
    /* Todo:  need o sort out things */

    /*
    |--------------------------------------------------------------------------
    | Email Notification
    |--------------------------------------------------------------------------
    */

    /* ==================== User CRUD ==================== */
    //    public function notifyCreated($data = [])
    //    {
    //        $title = app_name() . " Notification";
    //        $body  = 'Dear Customer, you have been successfully registered with us.';
    //
    //        $notification = $this->notification($title, $body, TypeEnum::UserCreated, [], $this);
    //        $this->notifyMobile($title, $body);
    //        $this->notify(new RegisteredNotification(['un_hashed_password' => $data['password']]));
    //
    //        return $this;
    //    }


    /*
    |--------------------------------------------------------------------------
    | Mobile Notification
    |--------------------------------------------------------------------------
    */

//    public function notifyMobile($body = '', $title = null)
//    {
//        $title ??= app_name() . ' Notification';
//        $data  = ['title' => $title, 'body' => $body];
//        if (isset($this->device_token)) send_device_notification($this->device_token, $data);
//    }


    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    */

//    public function notification($title, $body = '', $type = null, $data = [], $model = null)
//    {
//        $title    ??= app_name() . ' Notification';
//        $senderId = auth_check() ? auth_id() : null;
//        $status   = \App\Enums\Notification\StatusEnum::UnRead;
//        return notification_create($this->id, $title, $body, $senderId, $model, $data, $type, $status);
//    }

}
