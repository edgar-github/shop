<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchFilterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_hy' => $this->title_hy,
            'title_en' => $this->title_en,
            'slug' => $this->slug,
            'price' => $this->price,
            'main_image' => $this->main_image,
            'category_name' => $this->category->name_en,
            'authors' => AuthorsResource::collection($this->authors),
        ];
    }
}
