<?php

namespace App\Http\Controllers;

use App\Posts;
use App\Comments;
use Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display create post form
     *
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        return view('posts.create');
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

    public function store(Request $request)
    {
        //on_post, from_user, body
        $input['from_user'] = $request->user()->id;
        $input['on_post'] = $request->input('on_post');
        $input['body'] = $request->input('body');
        $slug = $request->input('slug');
        Comments::create( $input );
        return redirect($slug)->with('message', 'Comment published');
    }
}
