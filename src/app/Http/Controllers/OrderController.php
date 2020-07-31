<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Clients;
use App\Orders;
use App\Tariff;
use Carbon\Carbon;

use function GuzzleHttp\json_decode;

class OrderController extends Controller
{
    public function getTariffs()
    {
        $tariff = new Tariff();
        $return = $tariff->all();
        return response($return, 200);
    }

    public function createOrder(Request $request)
    {
        $tariff_id = $request->get('tarrif_id');
        $name = $request->get('name');
        $phone = $request->get('phone');
        $start = $request->get('start');
        $address = $request->get('address');

        $insert_order = [];
        
        //check the client
        $client_model = new Clients();
        $client = $client_model->where('phone', $phone)->first();

        if ($client === null) {
            $client_model->phone = $phone;
            $client_model->name = $name;
            $client_model->address = $address;
            $client_model->save();
            $insert_order['client_id'] = $client_model->id;
        } else {
            $insert_order['client_id'] = $client->id;
        }

        //check the tariff days

        $tariff_model  = new Tariff();
        $tariff = $tariff_model->where('id', $tariff_id)->first();
        
        if ($tariff === null) {
           return response('Bad request');
        } else {
            $insert_order['tariff_id'] = $tariff_id;
            $weekdays =  json_decode($tariff->weekdays);
            $client_weekday = Carbon::parse($start)->dayOfWeek;
            if($weekdays[$client_weekday]=='on'){
                $insert_order['start'] = Carbon::parse($start)->format('Y-m-d');
                //insert the order
                $order_model = new Orders();
                $order_model->insert($insert_order);
                return response('Ваш заказ подтвержден') ;
            }else{
                return response('Bad request');
            }
        }
       
    }
}
