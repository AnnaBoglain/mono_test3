<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Car;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
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
}
