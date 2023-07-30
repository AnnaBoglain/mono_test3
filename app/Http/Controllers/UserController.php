<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Car;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();
        $cars = Car::paginate(3);
        $count_on = DB::table('cars')->where('status', '=', "on")->count();
        $count = DB::table('cars')->count();
        return view('user.index', compact('users', 'cars', 'count_on', 'count'));
    }

    public function sum_car()
    {
        $cars = Car::all();

    }


    public function create()
    {
        return view('user.create');
    }

    public function store()
    {
        $data_user = request()->validate([
            'full_name' => 'required|string|min:3',
            'gender' => 'required|string',
            'tel' => 'required|string|unique:users',
            'address' => 'required|string',
        ]);

        $data_car = request()->validate([
            'stamp' => 'required|string',
            'model' => 'required|string',
            'body_color' => 'required|string',
            'state_number' => 'required|string|unique:cars',
            'status' => 'required|string',
        ]);

        if ($data_car['status'] != "on") {
            $data_car['status'] = "off";
        }
        DB::insert('insert into users (full_name, gender, tel, address) values (?, ?, ?, ?)', [$data_user['full_name'], $data_user['gender'], $data_user['tel'], $data_user['address']]);
        DB::insert('insert into cars (stamp, model, body_color, state_number, status, user_id) values (?, ?, ?, ?, ?, (SELECT id FROM users ORDER BY id DESC LIMIT 1))'
            , [$data_car['stamp'], $data_car['model'], $data_car['body_color'], $data_car['state_number'], $data_car['status']]);


        return redirect()->route('user.index');
    }

    public function store_car(Request $request, User $user)
    {
        $data_car = request()->validate([
            'stamp' => 'required|string',
            'model' => 'required|string',
            'body_color' => 'required|string',
            'state_number' => 'required|string|unique:cars',
            'status' => 'required|string',
        ]);
        if ($data_car['status'] != "on") {
            $data_car['status'] = "off";
        }
        DB::insert('insert into cars (stamp, model, body_color, state_number, status, user_id) values (?, ?, ?, ?, ?, ?)',
            [$data_car['stamp'], $data_car['model'], $data_car['body_color'], $data_car['state_number'], $data_car['status'], $user->id]);
        return redirect()->route('user.index');
    }


    public function update_user(User $user, Car $cars, Request $request)
    {
        $data_user = request()->validate([
            'full_name' => 'required|string|min:3',
            'gender' => 'required|string',
            'tel' => 'required|string',
            'address' => 'required|string',
        ]);

        //$car_id = 1;
        DB::update('update users
                set full_name = ?, gender = ?, tel = ?, address = ?
                where id = ?',
            [$data_user['full_name'], $data_user['gender'], $data_user['tel'], $data_user['address'], $user->id]);

        return redirect()->route('user.index');
    }

    public function update_car(Car $car, Request $request)
    {
        $data_car = request()->validate([
            'stamp' => 'required|string',
            'model' => 'required|string',
            'body_color' => 'required|string',
            'status' => 'required|string',
        ]);

        if ($data_car['status'] != "on") {
            $data_car['status'] = "off";
        }

        DB::update('update cars
                set stamp = ?, model = ?, body_color = ?, status = ?
                where id = ?',
            [$data_car['stamp'], $data_car['model'], $data_car['body_color'], $data_car['status'], $car->id]);
        return redirect()->route('user.index');
    }

    public function edit(User $user, Car $car)
    {
        $cars = Car::all();
        return view('user.edit', compact('user', 'cars'));
    }

    public function show(User $user)
    {
        return view('user.show', compact('user'));
    }


    public function delete(Car $car)
    {
        DB::delete('delete from cars where id = ?', [$car->id]);
        DB::delete('delete from users where id not in (select user_id from cars)');
        return redirect()->route('user.index');
    }

}