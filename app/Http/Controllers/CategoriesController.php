<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\RedirectIfNotParmitted;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class CategoriesController extends Controller
{
    public function __construct(){
        $this->middleware(RedirectIfNotParmitted::class.':category');
    }

    public function index()
    {
        return Inertia::render('Categories/Index', [
            'title' => 'Categories',
            'filters' => Request::all(['search']),
            'categories' => Category::orderBy('name')
                ->filter(Request::all(['search']))
                ->paginate(10)
                ->withQueryString()
                ->through(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'color' => $category->color,
                    ];
                } ),
        ]);
    }

    public function create()
    {
        return Inertia::render('Categories/Create',[
            'title' => 'Create a new category',
        ]);
    }

    public function store()
    {
        Category::create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'color' => ['nullable', 'max:50']
            ])
        );

        return Redirect::route('categories')->with('success', 'Category created.');
    }

    public function edit(Category $category) {
        return Inertia::render('Categories/Edit', [
            'title' => $category->name,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'color' => $category->color,
            ],
        ]);
    }

    public function update(Category $category)
    {
        $category->update(
            Request::validate([
                'name' => ['required', 'max:100'],
                'color' => ['nullable', 'max:50']
            ])
        );

        return Redirect::back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category){
        $category->delete();
        return Redirect::route('categories')->with('success', 'Category deleted.');
    }

    public function restore(Category $category){
        $category->restore();
        return Redirect::back()->with('success', 'Category restored.');
    }
}
