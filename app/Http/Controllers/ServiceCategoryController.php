<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = ServiceCategory::orderBy('name')->get();
        return view('categories.index', ['categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categories.create');
    }

    public function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'order' => ['required', 'numeric'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request)->validate();

        ServiceCategory::create($request->all());

        session()->flash('success', "Registro salvo com sucesso.");
        return redirect()->route('categories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceCategory $category)
    {
        return view('categories.show', ['category' => $category]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ServiceCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceCategory $category)
    {
        return view('categories.edit', ['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ServiceCategory $category)
    {
        $this->validator($request)->validate();

        $category->name = $request->input('name');
        $category->order = $request->input('order');
        $category->save();

        session()->flash('success', "Registro salvo com sucesso.");
        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceCategory $category)
    {
        $category->delete();
        session()->flash('success', "Registro excluído com sucesso.");
        return redirect()->route('categories.index');
    }
}
