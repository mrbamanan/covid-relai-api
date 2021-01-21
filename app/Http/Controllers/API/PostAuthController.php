<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Author;
use App\Models\File;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PostAuthController extends Controller
{
    /**
     * PostAuthController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $posts = Post::with(['files', 'author', ]);
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
        $posts = Post::with(['files', 'author'])->where('active','=', 0);
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
            'medias' => 'nullable|mimes|mimes:jpg,jpeg,png,mp4,mpeg,pdf'
        ]);

        DB::beginTransaction();
        try {
            $author = Author::first()->where('email', $author_data['email']);

            if (empty($author)) {
                $author = Author::create([
                    'name' => 'Administrateur',
                    'email' => Auth::user()->email,
                    'phone' => empty($author_data['phone']) ? "" : $author_data['phone'],
                    'address' => empty($author_data['address']) ? "" : $author_data['address'],
                    'job' => empty($author_data['job']) ? "" : $author_data['job'],
                ]);
            }

            $post = $author->posts()->create([
                'title' => $post_data['title'],
                'slug' => Str::slug($post_data['title']),
                'place' => empty($post_data['place']) ? "" : $post_data['place'],
                'body' => $post_data['body'],
                'source' => $post_data['source'],
            ]);

            if ($post_data->has('medias')) {

                foreach ($post_data->medias as $media) {
                    $file = new File();

                    $fileName = $post->id . '_' . $media->getClientOriginalName();
                    $filePath = $media->storeAs('medias', $fileName, 'public');

                    $file->name = $fileName;
                    $file->path = '/storage/' . $filePath;

                    $post->files()->save($file);
                }
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => "Erreur lors de la création", $ex], 400);
        }

        return (new PostResource($post->loadMissing(['files', 'author'])))->response();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
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
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|unique:posts,title,' . $id,
            'place' => 'sometimes|string',
            'body' => 'sometimes|string',
            'source' => 'sometimes',
            'comment' => 'sometimes|string'
        ]);

        $post = Post::find($id);

        if (empty($post)) {
            return response()->json(['error' => 'Aucun enregistrement trouvé'], 409);
        }

        DB::beginTransaction();
        try {
            $post->fill([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'place' => $data['place'],
                'body' => $data['body'],
                'source' => $data['source'],
            ]);


            $post->save();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => 'Echec modification', 'data' => $post], 409);
        }

        return (new PostResource($post->loadMissing(['author', 'files'])))->response();
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function stateUpdate(Request $request, $id)
    {
        $request->validate([
            'active' => 'required|boolean'
        ]);

        $post = Post::find($id);

        DB::beginTransaction();
        try {
            $post->active = $request->active;
            $post->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['error' => 'Action non autorisée'], 409);
        }
        return response()->json(['success' => 'Statut modifié'], 200);
    }


    /**
     * @param Request $request
     * @param $id
     */
    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $post = Post::find($id);
        $post->comment = $request->comment;
        $post->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $post = Post::find($id);

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
