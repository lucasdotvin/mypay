<?php

namespace App\Services\Payments\Notification\MocklabNotification;

enum NotificationResult: string
{
    case Success = 'Success';
    case Fail = 'Fail';
}
