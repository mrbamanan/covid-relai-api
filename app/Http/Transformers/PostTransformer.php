<?php


namespace App\Http\Transformers;


use App\Models\File;
use App\Models\Post;
use Illuminate\Support\Str;

class PostTransformer
{
    /**
     * @param array $input
     * @param Post|null $post
     * @return Post|mixed|null
     */

    public static function toInstance(array $input, Post $post = null){
        if (empty($post)) $post = new Post();

        foreach ($input as $key => $value){
            switch ($key){
                case 'title':
                    $post->title = $value;
                    $post->slug = Str::slug($value);
                    break;
                case 'place':
                    $post->place = $value;
                    break;
                case 'body':
                    $post->body = $value;
                    break;
                case 'source':
                    $post->source = $value;
                    break;
                case 'comment':
                    $post->comment = $value;
                    break;
                case 'active':
                    $post->active = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'badge':
                    $post->badge = $value;
                    break;
                default:
                    break;
            }
        }
        return $post;
    }
}
