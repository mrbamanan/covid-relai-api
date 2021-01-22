<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Transformers\AuthorTransformer;
use App\Http\Transformers\PostTransformer;
use App\Models\Author;
use App\Models\File;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewPostNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class PostController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $posts = Post::with(['files', 'author',]);

      /*  if (!Auth::check()) {
            $posts = Post::with(['files', 'author'])->where('active', '=', true);


        }*/

        return PostResource::collection($posts->get())->response();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function actives()
    {
        $posts = Post::with(['files', 'author'])->where('active');
        return PostResource::collection($posts->get())->response();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function inActives()
    {
        $posts = Post::with(['files', 'author'])->where('active', '=', 0);
        return PostResource::collection($posts->get())->response();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $author_data = $request->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'job' => 'nullable|string'
        ]);

        $post_data = $request->validate([
            'title' => 'required|string|unique:posts',
            'place' => 'nullable|string',
            'body' => 'required|string',
            'source' => 'required|string',
            'files' => 'nullable|mimes:jpg,jpeg,png,mp4,mpeg,pdf'
        ]);


        DB::beginTransaction();
        try {

            $newAuthor = AuthorTransformer::toInstance($author_data);

            $author = Author::updateOrCreate(
                ['email' => $author_data['email']],
                $newAuthor->toArray()
            );

            $newPost = PostTransformer::toInstance($post_data);
            $post = $author->posts()->save($newPost);

            if ($request->hasFile('files')) {
                $files = $request->file('files');
                foreach ($files as $file) {

                    $fileName = $post->id . '_' . $file->getClientOriginalName();

                    $filePath = $file->storeAs('files', $fileName, 'public');

                    $newFile = new File();
                    $newFile->name = $fileName;
                    $newFile->path = '/storage/' . $filePath;

                    $post->files()->save($newFile);
                }
            }
            $post->refresh();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => "Erreur lors de la création", $ex], 400);
        }

        //Notify users
        Notification::send(User::all(), new NewPostNotification($post));

        return (new PostResource($post->loadMissing(['author', 'files'])))->response();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post)
    {

        if (empty($post)) {
            return response()->json(['error' => 'Aucune donnée à afficher'], 201);
        }

        return (new PostResource($post->loadMissing(['files', 'author'])))->response();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|unique:posts,title,' . $post->id,
            'place' => 'sometimes|string',
            'body' => 'sometimes|string',
            'source' => 'sometimes|string',
            'active' => 'sometimes|boolean',
            'comment' => 'sometimes|string',
            'badge' => 'sometimes|string'
        ]);

        if (empty($post)) {
            return response()->json(['error' => 'Aucun enregistrement trouvé'], 409);
        }

        DB::beginTransaction();
        try {
            $newPost = PostTransformer::toInstance($data);
           // dd($newPost->active);
            $post->update($newPost->toArray());

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => 'Echec modification', 'data' => $post], 409);
        }

        return (new PostResource($post->loadMissing(['author', 'files'])))->response();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post)
    {

        if (empty($post)) {
            return response()->json(['error' => 'Aucun enregistrement trouvé'], 409);
        }

        DB::beginTransaction();
        try {
            $post->delete();
            $post->active = false;
            $post->save();

            DB::commit();
        } catch (\Exception $ex) {
            Log::info($ex->getMessage());
            DB::rollBack();
            return response()->json(['error' => 'Echec suppression'], 409);
        }

        return response()->json(['success' => 'Supprimé avec succès'], 200);
    }
}
