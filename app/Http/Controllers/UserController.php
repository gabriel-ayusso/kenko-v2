<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::orderBy('firstname')->get();
        return view('users.create', ['employees' => $employees]);
    }

    public function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'employee_id' => ['nullable', 'numeric', 'exists:employees,id']
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
        $request->validate(['email' => ['unique:users,email']]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make(Str::random(48)),
            'facebook_avatar' =>  '',
            'facebook_token' =>  '',
            'admin' => $request->boolean('admin'),
            'manager' => $request->boolean('manager'),
            'active' => $request->boolean('active'),
            'agenda' => $request->boolean('agenda'),
        ]);

        $token = Password::getRepository()->create($user);
        $user->sendPasswordResetNotification($token);

        session()->flash('success', "Usuário salvo com sucesso. Um link foi enviado para {$user->email} para que ele possa criar sua senha");
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $employees = Employee::orderBy('firstname')->get();
        return view('users.show', ['user' => $user, 'employees' => $employees]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $employees = Employee::orderBy('firstname')->get();
        return view('users.edit', ['user' => $user, 'employees' => $employees]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validator($request)->validate();

        if ($request->email != $user->email)
            $request->validate(['email' => ['unique:users,email']]);

        $user->employee_id = $request->input('employee_id') == 0 ? null : $request->input('employee_id');
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->admin = $request->boolean('admin');
        $user->manager = $request->boolean('manager');
        $user->active = $request->boolean('active');
        $user->agenda = $request->boolean('agenda');
        $user->save();

        session()->flash('success', "Usuário salvo com sucesso.");
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function passwordReset(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $token = Password::getRepository()->create($user);
        $user->sendPasswordResetNotification($token);

        session()->flash('success', "Link para alteração de senha enviado com sucesso.");
        return redirect()->route('users.edit', ['user' => $user]);
    }
}
