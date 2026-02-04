<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LeaveRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'reason' => $this->reason,
            'attachment_url' => $this->getAttachmentUrl(),
            'status' => $this->status,
            'approved_by' => $this->approved_by,
            'total_days' => $this->total_days,
            'created_at' => $this->created_at,
        ];
    }

    private function getAttachmentUrl(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->url($this->attachment_path);
    }
}
