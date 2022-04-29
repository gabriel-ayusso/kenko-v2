<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Service::class, 'service');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::with(['category' => function ($query)
        {
            $query->orderBy('name');
        }])->get();
        return view('services.index', ['services' => $services]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ServiceCategory::orderBy('name')->get();
        return view('services.create', ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'time' => 'required',
            'price' => 'required',
            'comission' => 'required',
            'category_id' => 'required',
        ]);

        $service = Service::create($request->all());
        session()->flash('success', "Serviço salvo com sucesso.");
        return redirect()->route('services.edit', $service);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        return view('services.show', ['service' => $service]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        $categories = ServiceCategory::orderBy('name')->get();
        $caServices = DB::select("select id, name from ca_services order by name");
        return view('services.edit', [
            'service' => $service,
            'categories' => $categories,
            'caServices' => $caServices,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'time' => 'required',
            'price' => 'required',
            'comission' => 'required',
            'category_id' => 'required',
        ]);

        $service->name = $request->input('name');
        $service->description = $request->input('description');
        $service->time = $request->input('time');
        $service->price = $request->input('price');
        $service->comission = $request->input('comission');
        $service->private = $request->boolean('private');
        $service->category_id = $request->input('category_id');
        $service->ca_id = $request->input('ca_id');
        $service->save();

        session()->flash('success', "Serviço alterado com sucesso.");
        return redirect()->route('services.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        $service->delete();
        session()->flash('success', "Serviço excluído com sucesso.");
        return redirect()->route('services.index');
    }

    public function storeAvatar(Request $request, Service $service)
    {
        $request->validate([
            'avatar' => 'required|image|max:5120',
        ]);

        $img = Image::make($request->file('avatar'));

        Storage::put("service_{$service->id}/avatar.png", $img->encode('png'));

        return redirect()->route('services.edit', ['service' => $service]);
    }

    public function getAvatar(Service $service)
    {
        if (Storage::exists("service_{$service->id}/avatar.png"))
            return Storage::download("service_{$service->id}/avatar.png");
        else
            return Storage::download('public/img/massage.png');
    }
}
