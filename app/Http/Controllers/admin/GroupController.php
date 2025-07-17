<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Category;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    // public function index()
    // {
    //     $groups = Group::with(['category', 'matchups.teamA', 'matchups.teamB'])->paginate(10);

    //     $categories = Category::with('groups')->get();
    //     return view('admin.tables.groups.groups', compact('groups', 'categories'));
    // }

    public function create()
    {
        $categories = Category::all();
        return view('admin.tables.groups.groups-create-modal', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        Group::create($request->only('name', 'category_id'));

        return redirect()->route('matchups.index')->with('success', 'Group created.');
    }

    public function edit(Group $group)
    {
        $categories = Category::all();
        return view('admin.tables.groups.groups-edit-modal', compact('group', 'categories'));
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $group->update($request->only('name', 'category_id'));

        return redirect()->route('matchups.index')->with('success', 'Group updated.');
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return redirect()->route('matchups.index')->with('success', 'Group deleted.');
    }
}
