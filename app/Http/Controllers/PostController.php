<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostFormRequest;
use App\Posts;

class PostController extends Controller
{
    public function __construct()
    {
        // If user isn't logged in this will redirect to the login page.
        $this->middleware('auth');
    }

    /**
     * Display create post form
     *
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        // if user can post i.e. user is admin or author
        if($request->user()->can_post())
        {
            return view('posts.create');
        }
        else
        {
            return redirect('/')->withErrors('You have not sufficient permissions for writing post');
        }
    }

    /**
     * Write the post to database.  Takes input from create post form above.
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $post = new Posts();
        $post->title = $request->get('title');
        $post->body = $request->get('body');
        $post->slug = str_slug($post->title);
        $post->author_id = $request->user()->id;
        if($request->has('save'))
        {
            $post->active = 0;
            $message = 'Post saved successfully';
        }
        else
        {
            $post->active = 1;
            $message = 'Post published successfully';
        }
        $post->save();
        return redirect('edit/'.$post->slug)->withMessage($message);
    }

    /**
     * Display existing post in edit form.
     *
     * @param Request $request
     * @param $slug
     * @return $this
     */
    public function edit(Request $request,$slug)
    {
        $post = Posts::where('slug',$slug)->first();
        if($post && ($request->user()->id == $post->author_id || $request->user()->is_admin()))
            return view('posts.edit')->with('post',$post);
        return redirect('/')->withErrors('you have not sufficient permissions');
    }

    /**
     * Write changes from derived from edit form to database
     *
     * @param Request $request
     * @return $this
     */
    public function update(Request $request)
    {
        $id = $request->input('post_id');
        $post = Posts::find($id);
        if($post && ($post->author_id == $request->user()->id || $request->user()->is_admin()))
        {
            $title = $request->input('title');
            $slug = str_slug($title);
            $duplicate = Posts::where('slug',$slug)->first();
            if($duplicate)
            {
                if($duplicate->id != $id)
                {
                    return redirect('edit/'.$post->slug)->withErrors('Title already exists.')->withInput();
                }
                else
                {
                    $post->slug = $slug;
                }
            }
            $post->title = $title;
            $post->body = $request->input('body');
            if($request->has('save'))
            {
                $post->active = 0;
                $message = 'Post saved successfully';
                $landing = 'edit/'.$post->slug;
            }
            else {
                $post->active = 1;
                $message = 'Post updated successfully';
                $landing = $post->slug;
            }
            $post->save();
            return redirect($landing)->withMessage($message);
        }
        else
        {
            return redirect('/')->withErrors('you have not sufficient permissions');
        }
    }

    /**
     * @param $slug
     * @return $this
     */
    public function show($slug)
    {
        $post = Posts::where('slug',$slug)->first();
        if(!$post)
        {
            return redirect('/')->withErrors('requested page not found');
        }
        $comments = $post->comments;
        return view('posts.show')->withPost($post)->withComments($comments);
    }

    /**
     * List all posts from logged in user
     *
     * @param $id
     * @return mixed
     */
    public function list($id)
    {
        $posts = Posts::where('author_id',$id)->orderBy('created_at','desc')->paginate(5);
        $title = 'Latest Posts';
        return view('list')->withPosts($posts)->withTitle($title);
        return view('list')->withTitle('list');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $post = Posts::find($id);
        if($post && ($post->author_id == $request->user()->id || $request->user()->is_admin()))
        {
            $post->delete();
            $data['message'] = 'Post deleted Successfully';
        }
        else
        {
            $data['errors'] = 'Invalid Operation. You have not sufficient permissions';
        }
        return redirect('/')->with($data);
    }
}

