<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomController extends Controller
{
	
	/**
	* Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
		return response()->json($this->fetchAll(), 200);
	}

    public function fetchAll() {
        return \App\Room::lazyLoad()->orderBy('created_at', 'DESC')->get();
    }
 	
	
	/**
	* Store a newly created resource in storage.
        *
        * @param  \Illuminate\Http\Request  $request
        * @return \Illuminate\Http\Response
        */
    public function store(Request $request)
    {
		$room = new \App\Room;
        $name = 'Studio Standard-' .time();
        $request->merge(array(
            'name' => $name,
            'slug' => $name,
        ));

        $room->name = $request->input('name');
        $room->slug = $request->input('slug');
        $room->roomSize = $request->input('roomSize', 0);
        $room->totalRooms = $request->input('totalRooms', 0);
        $room->totalPerson = $request->input('totalPerson', 1);
        $room->location = $request->input('location', '');
        $room->extraBed = $request->input('extraBed', 0);
        $room->bathrooms = $request->input('bathrooms', 0);
        $room->building = $request->input('building', 0);
        $room->isActive = $request->input('isActive', 0);
        $room->sort = $request->input('sort', 0);
        try {
            if ($room->save()) {
                return response()->json($this->fetchAll(), 200, [], JSON_UNESCAPED_UNICODE);   
            }
        } catch(\Exception $e) {
            \Log::info('ERROR: '.$e->getMessage());
            return response()->json('Oops! Error please report to administrator.', 400, [], JSON_UNESCAPED_UNICODE);
        }
    

        return response()->json($validator->errors()->getMessages(), 400, [], JSON_UNESCAPED_UNICODE);
	}
	
	
	/**
	* Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id)
    {
        $room = \App\Room::findOrFail($id);
        $room->rules['name'] = $room->rules['name'] . ','. $room->id . ',id';
        $room->rules['slug'] = $room->rules['slug'] . ','. $room->id . ',id';
		$validator = $room->validate($request->input(), $room->rules);
		if ($validator->passes()) {
			$room->name = $request->input('name');
			$room->slug = str_slug($request->input('name'));
			$room->roomSize = $request->input('roomSize', 0);
			$room->totalRooms = $request->input('totalRooms', 0);
			$room->totalPerson = $request->input('totalPerson', 1);
			$room->location = $request->input('location', '');
			$room->extraBed = $request->input('extraBed', 0);
			$room->bathrooms = $request->input('bathrooms', 0);
			$room->building = $request->input('building', 0);
			$room->isActive = $request->input('isActive', 0);
			$room->sort = $request->input('sort', 0);
			try {
                if ($room->save()) {
                    $rates = $request->input('roomRates');
                    if ($rates) {
                        foreach($rates as $rateID => $r) { 
                            if ($r) {
                                $isActive = $r['price'] > 0 ? $r['isActive'] : 0;
                                $a[$rateID] = array('price' => (float)$r['price'], 'isActive'  => $isActive );
                                $room->rates()->sync($a);
                            }
                        }
                            
                    }
                    $countMonthlyRate = 0;
                    $countRegRate = 0;
                    foreach($room->rates()->get() as $r) {
                        if ($r->isMonthly && $r->pivot->isActive) {
                            $countMonthlyRate +=1;
                        } else if ($r->pivot->isActive) {
                            $countRegRate +=1;
                        }
                    }
                    //Disable room if more than 1 monthly rate
                    if ($countRegRate == 0 || $countMonthlyRate != 1) {
                        $room->isActive  = 0;
                        $room->save();
                        return response()->json('Must have 1 monthly rate and must have regular rate enabled.', 400, [], JSON_UNESCAPED_UNICODE);   
                    }
                    
                    return response()->json($this->fetchAll(), 200, [], JSON_UNESCAPED_UNICODE);   
                }
            }catch(\Exception $e) {
                \Log::info('ERROR: '.$e->getMessage());
                return response()->json('Oops! Error please report to administrator.', 400, [], JSON_UNESCAPED_UNICODE);   
            }
		}
        $validationStr = '';
        foreach ($validator->errors()->getMessages() as $k => $error) {
            foreach ($error as $err) {
                $validationStr .= $err .'<br/>';
            }
            
        }

        return response()->json($validationStr, 400, [], JSON_UNESCAPED_UNICODE);
	}
	
	
	/**
	* Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {   try {
            \App\Room::findOrFail($id)->delete();
            return response()->json($this->fetchAll(), 200, [], JSON_UNESCAPED_UNICODE);   
        } catch(\Exception $e) {
            \Log::info('ERROR: '.$e->getMessage());
            return response()->json('Oops! Error please report to administrator.', 400, [], JSON_UNESCAPED_UNICODE);   
        }
	}

    public function attachAminities(Request $request) {
        $room = \App\Room::find($request->input('roomID'));
        $aminites = json_decode($request->input('aminities'));
        if ($room && count($aminites) != 0) {
            if ($room->aminities()->sync($aminites)) {

                $r = $room->aminities()->pluck('id')->all();

                return response()->json($r, 200, [], JSON_UNESCAPED_UNICODE);
            }
        }

        return response()->json('failed', 400);
    }

    public function attachBeds(Request $request, $roomID) {
        try {
            $room = \App\Room::find($roomID);
            $beds = $request->input('beds');
            if ($beds) {
                foreach ($beds as $i){
                    $bed = new \App\Bed;
                    if (isset($i['id']) && strpos($i['id'], '_static_') === false) {
                        $bed = \App\Bed::find($i['id']);
                    }
                    $bed->qty = $i['qty'];
                    $bed->type = $i['type'];
                    
                    $room->beds()->save($bed);
                }

                return response()->json($room->beds()->get()->toArray(), 200, [], JSON_UNESCAPED_UNICODE);
            }
        } catch(\Exception $e) {
            \Log::info('ERROR: '.$e->getMessage());
            return response()->json('Oops! Error please report to administrator.', 400, [], JSON_UNESCAPED_UNICODE);
        }

    }

    public function detachBed(Request $request, $roomID, $bedID) {
         $bed = \App\Bed::find($bedID);
         if ($bed) 
            $bed->delete();
    }

    public function types(Request $request) {
        try {
            $types = \App\Room::with(array('rates' => function($q) {
                $q->where('isMonthly', 0);
                $q->wherePivot('isActive', 1);
            }))
                ->select(['*', \DB::raw('CONCAT(name, " ", building, " - ID ", id) AS title')])
                ->where('isActive', 1)
                ->get()->toArray();
            return response()->json($types, 200, [], JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e) {
            \Log::info('ERROR: '.$e->getMessage());
            return response()->json($e->getMessage(), 400, [], JSON_UNESCAPED_UNICODE);
        }
        
    }

    public function showBySlug(Request $request, $slug) {
         $room = \App\Room::lazyLoad()->where(['slug' => $slug, 'isActive' => 1])->first();
         
         return response()->json($room->toArray(), 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function showAvailable()
    {
		return response()->json(\App\Room::lazyLoad()->where('isActive', 1)->orderBy('created_at', 'DESC')->get(), 200);
	}

    
}
