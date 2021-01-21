<?php


namespace App\Http\Transformers;


use App\Models\Author;

class AuthorTransformer
{
    /**
     * @param array $input
     * @param Author|null $author
     * @return Author|null
     */

    public static function toInstance(array $input, Author $author = null){
        if (empty($author)){
            $author = new Author();
        }

        foreach ($input as $key => $value){
            switch ($key){
                case "name":
                    $author->name = $value;
                    break;
                case 'email':
                    $author->email = $value;
                    break;
                case 'phone':
                    $author->phone = $value;
                    break;
                case 'address':
                    $author->address = $value;
                    break;
                case 'job':
                    $author->job = $value;
                    break;
            }
        }
        return $author;
    }
}
