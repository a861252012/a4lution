<?php
/*

=========================================================
* Argon Dashboard PRO - v1.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard-pro-laravel
* Copyright 2018 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)

* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

*/
namespace App\Http\Controllers;

use App\Tag;
use App\User;
use App\Http\Requests\TagRequest;

class TagController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Tag::class);
    }

    /**
     * Display a listing of the tags
     *
     * @param \App\Tag  $model
     * @return \Illuminate\View\View
     */
    public function index(Tag $model)
    {
        $this->authorize('manage-items', User::class);

        return view('tags.index', ['tags' => $model->all()]);
    }

    /**
     * Show the form for creating a new tag
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('tags.create');
    }

    /**
     * Store a newly created tag in storage
     *
     * @param  \App\Http\Requests\TagRequest  $request
     * @param  \App\Tag  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TagRequest $request, Tag $model)
    {
        $model->create($request->all());

        return redirect()->route('tag.index')->withStatus(__('Tag successfully created.'));
    }

    /**
     * Show the form for editing the specified tag
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\View\View
     */
    public function edit(Tag  $tag)
    {
        return view('tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\TagRequest  $request
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TagRequest $request, Tag $tag)
    {
        $tag->update($request->all());

        return redirect()->route('tag.index')->withStatus(__('Tag successfully updated.'));
    }

    /**
     * Remove the specified tag from storage
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tag $tag)
    {
        if (!$tag->items->isEmpty()) {
            return redirect()->route('tag.index')->withErrors(__('This tag has items attached and can\'t be deleted.'));
        }

        $tag->delete();

        return redirect()->route('tag.index')->withStatus(__('Tag successfully deleted.'));
    }
}
