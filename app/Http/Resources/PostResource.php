<?php

namespace App\Http\Resources;

use App\Models\Author;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'post' =>[
                'id' => $this->id,
                'title' => $this->title,
                'slug' => $this->slug,
                'body' => $this->body,
                'place' => $this->place,
                'source' => $this->source,
                'active' => $this->active,
                'badge' => $this->badge,
                'comment' => $this->comment,
                'files' => FileResource::collection($this->whenLoaded('files'))
            ],
            'author' => (new AuthorResource($this->whenLoaded('author'))),
        ];
    }
}
