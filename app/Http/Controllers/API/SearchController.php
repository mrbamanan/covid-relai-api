<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $data = $request->validate([
            'title' => 'sometimes|string',
            'place' => 'sometimes|string',
            'body' => 'sometimes|string',
            'source' => 'sometimes|string'
        ]);

        $queries = Post::query();

        foreach ($data as $key => $value){
            switch ($key){
                case 'title':
                $queries->where('title', $value)
                    ->orWhere('title', 'LIKE', '%'.$value)
                    ->orWhere('title', 'LIKE', $value.'%')
                    ->orWhere('title', 'LIKE', '%'.$value.'%');
                break;
                case 'body':
                    $queries->where('body', $value)
                        ->orWhere('body', 'LIKE', '%'.$value)
                        ->orWhere('body', 'LIKE', $value.'%')
                        ->orWhere('body', 'LIKE', '%'.$value.'%');

                    break;
                case 'place':
                    $queries->where('place', $value)
                        ->orWhere('place', 'LIKE', '%'.$value)
                        ->orWhere('place', 'LIKE', $value.'%')
                        ->orWhere('place', 'LIKE', '%'.$value.'%');
                    break;
                case 'source':
                    $queries->where('source', $value)
                        ->orWhere('source', 'LIKE', '%'.$value)
                        ->orWhere('source', 'LIKE', $value.'%')
                        ->orWhere('source', 'LIKE', '%'.$value.'%');
                    break;
                default:
                    break;
            }
        }

        $posts = $queries->get();

        return PostResource::collection($posts)->response();

    }
}
