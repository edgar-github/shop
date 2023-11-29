<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title_hy'   => $this->title_hy,
            'title_en'   => $this->title_en,
            'slug'       => $this->slug,
            'image'      => URL::to('storage/' . $this->image),
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
