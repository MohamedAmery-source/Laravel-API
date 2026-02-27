<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'notification_id' => $this->notification_id,
            'user_id' => $this->user_id,
            'message' => $this->message,
            'notification_type' => $this->notification_type,
            'related_request_id' => $this->related_request_id,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
