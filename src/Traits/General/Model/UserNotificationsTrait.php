<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Traits\General\Model;

use AbdullahMateen\LaravelHelpingMaterial\Enums\Notification\TypeEnum;
use App\Notifications\User\RegisteredNotification;

trait UserNotificationsTrait
{
    /*
    |--------------------------------------------------------------------------
    | Email Notification
    |--------------------------------------------------------------------------
    */

    /* ==================== User CRUD ==================== */
    public function notifyCreated($data = []): static
    {
        $title = app_name() . " Notification";
        $body  = 'Dear Customer, you have been successfully registered with us.';

        $notification = $this->notification($title, $body, TypeEnum::UserCreated, [], $this);
        $this->notifyMobile($title, $body);
        $this->notify(new RegisteredNotification(['un_hashed_password' => $data['password']]));

        return $this;
    }

    public function notifyUpdated()
    {
        return $this;
    }

    public function notifyDeleted()
    {
        return $this;
    }

    public function notifyPromote($level = null)
    {
        return $this;
    }

    /* ==================== Program Assign ==================== */
    public function notifyProgramAssign($program)
    {
        return $this;
    }


    /*
    |--------------------------------------------------------------------------
    | Mobile Notification
    |--------------------------------------------------------------------------
    */

    public function notifyMobile($body, $title)
    {
        $title ??= app_name() . ' Notification';
        $data = ['title' => $title, 'body' => $body ?? ''];
        if (isset($this->fcm_token)) send_mobile_notification($this->fcm_token, $data);
    }


    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    */

    public function notification($title, $body = '', $type = null, $data = [], $model = null)
    {
        $title ??= app_name() . ' Notification';
        $senderId = auth_check() ? auth_id() : null;
        $status   = \App\Enums\Notification\StatusEnum::UnRead;
        return notification_create($this->id, $title, $body, $senderId, $model, $data, $type, $status);
    }

}