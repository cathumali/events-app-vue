<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Calendar;

class CalendarController extends Controller
{
	public function events(){
		$event = Calendar::all();
		return response()->json($event);
	}
 	public function store(Request $request)
    {	

    	foreach ($request->all() as $key => $event) {

	        $event = new Calendar([
	            'event_name' => $event['name'],
	            'event_date' => $event['date']
	        ]);
	        $event->save();
    	}

        return response()->json($event);
    }
}