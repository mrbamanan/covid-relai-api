<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\PostResource;
use App\Http\Transformers\AuthorTransformer;
use App\Http\Transformers\PostTransformer;
use App\Models\Author;
use App\Models\File;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewPostNotification;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;


class PostController extends Controller
{
    /**
     * PostController constructor.
     */
    public function __construct()
    {
        $this->middleware('api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $posts = Post::with(['files', 'author'])->where('active');
        return PostResource::collection($posts->get())->response();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'author.name' => 'required|string|min:3',
            'author.email' => 'required|email',
            'author.phone' => 'nullable|string',
            'author.address' => 'nullable|string',
            'author.job' => 'nullable|string',

            'post.title' => 'required|string|unique:posts,title',
            'post.place' => 'nullable|string',
            'post.body' => 'required|string',
            'post.source' => 'required|string',
            'post.files.*' => 'nullable|mimes:jpg,jpeg,png,mp4,mpeg,pdf',

        ]);


        DB::beginTransaction();
        try {


            $newAuthor = AuthorTransformer::toInstance($data['author']);

            $author = Author::updateOrCreate(
                ['email' => $data['author']['email']],
                $newAuthor->toArray()
            );

            $newPost = PostTransformer::toInstance($data['post']);

            $post = $author->posts()->save($newPost);

            if ($request->hasFile('post.files')) {
                $files = $request->file('post.files');
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

        // return (new AuthorResource($author))->response();
        return (new PostResource($post->loadMissing(['author', 'files'])))->response();

    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {

        $post = Post::find($id);
        if (empty($post)) {
            return response()->json(['error' => 'Aucune donnée à afficher'], 201);
        }

        return (new PostResource($post->loadMissing(['files', 'author'])))->response();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        //
    }
}
