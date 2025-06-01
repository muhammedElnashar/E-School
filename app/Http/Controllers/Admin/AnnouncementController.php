<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $announcements = Announcement::paginate(5);
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(StoreAnnouncementRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('announcement', 'images');
                $data["image"] = $path;
        }
        Announcement::create($data);

        return redirect()->route('announcements.index')
                         ->with('success', 'Announcement created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {

            // Delete the old image if it exists
            if ($announcement->image) {
                Storage::disk('images')->delete($announcement->image);
            }
            $path = $request->file('image')->store('announcement', 'images');
            $data["image"] = $path;
        }
        $announcement->update($data);
        return redirect()->route('announcements.index')
                         ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(Announcement $announcement)
    {
        if ($announcement->image) {
            Storage::disk('images')->delete($announcement->image);
        }
        $announcement->delete();
        return redirect()->route('announcements.index')
                         ->with('success', 'Announcement deleted successfully.');
    }


    }

