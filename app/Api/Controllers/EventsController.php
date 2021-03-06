<?php

namespace Api\Controllers;

// use DB;
use Auth;
use App\Event;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
// use Api\Requests\EventRequest;
use Api\Transformers\EventTransformer;

use Carbon\Carbon;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection as FractalCollection;

function parseAddress($address)
{
    $curl     = new \Ivory\HttpAdapter\CurlHttpAdapter();
    $geocoder = new \Geocoder\Provider\GoogleMaps(
        $curl,
        'zh-tw',
        'tw',
        true,
        'AIzaSyBGogPR8JvLm5xC8xGwSTCpKkXm5eZFVH4'
    );

    $geoResults = $geocoder->geocode($address)->first();

    return $geoResults;
}

/**
 * @Resource('Events', uri='/events')
 */
class EventsController extends BaseController
{
    public function index(Request $request)
    {
      if (isset($request->per_page)) {
        $per_page = (int) $request->per_page;
      }
      $events = Event::orderBy('date', 'desc')->paginate($per_page ?? 25);
      return $this->response->paginator($events, new EventTransformer);
    }

    /**
     * Store a new dog in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $location = Location::firstOrCreate([
            'address'   => $request->address,
            'lat'       => $request->latitude,
            'lng'       => $request->longitude,
            'region_id' => $request->region,
            'name'      => $request->location,
        ]);

        $date = new Carbon($request->date);

        $event = Event::create([
            'date'    => $date->format('Y-m-d'),
            'start'   => $request->start,
            'end'     => $request->end,
            'name'    => $request->name,
            'url'     => $request->url,
            'location_id' => $location->id,
            'user_id' => Auth::user()->id
        ]);
        $event->politicians()->attach($request->politician);
        return item($event, new EventTransformer);
    }

    public function batchStore(Request $request)
    {
        $geoResults = parseAddress($request->address);
        $region = Region::where('postal_code', $geoResults->getPostalCode())->first();
        $date = new Carbon($request->date);

        if ($region) {
            $location = Location::firstOrCreate([
                'address'   => $request->address,
                'lat'       => $geoResults->getLatitude(),
                'lng'       => $geoResults->getLongitude(),
                'region_id' => $region->id,
                'name'      => $request->location,
            ]);
            $event = Event::firstOrCreate([
                'date'    => $date->format('Y-m-d'),
                'name'    => $request->name,
                'user_id' => Auth::user()->id
            ]);
            $event->location_id = $location->id;
        } else {
            $event = Event::firstOrCreate([
                'date'    => $date->format('Y-m-d'),
                'name'    => $request->name,
                'user_id' => Auth::user()->id
            ]);
        }

        $event->url = $request->url;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->description = $request->description;
        $event->save();

        $eventType = EventCategory::where([
            'parent_id' => $request->parentId,
            'name' => $request->category == '' ? '無分類' : $request->category,
        ])->first();

        if ($eventType) {
            $event->categories()->detach();
            $event->categories()->attach($eventType->id);
        }

        // must detach ?
        $event->politicians()->detach();
        $event->politicians()->attach($request->politician);
        return $this->item($event, new EventTransformer);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      try {
        return $this->response->item(Event::findOrFail($id), new EventTransformer);
      } catch (ModelNotFoundException $e) {
        return $this->response->errorNotFound();
      }

    }

    /**
     * Update the Event in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->name = $request->name;
        $event->description = $request->description;
        $event->date = $request->date;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->url = $request->url;

        if ($request->address) {
            $geoResults = parseAddress($request->address);

            $region = Region::where('postal_code', $geoResults->getPostalCode())->first();
            $location = Location::firstOrCreate([
                'address'   => $request->address,
                'lat'       => $geoResults->getLatitude(),
                'lng'       => $geoResults->getLongitude(),
                'region_id' => $region->id,
                'name'      => $request->location,
            ]);
            $event->location_id = $location->id;
        }

        $event->save();

        $eventType = EventCategory::firstOrCreate([
            'parent_id' => (int) $request->parentId,
            'name' => $request->category == '' ? '無分類' : $request->category,
        ]);
        $eventType->makeChildOf($eventTypeRoot);

        $event->categories()->detach();
        $event->categories()->attach($eventType->id);

        // must detach ?
        $event->politicians()->detach();
        $event->politicians()->attach($request->politician);

        return response()->json([
            'status' => '201'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Event::destroy($id);
    }
}
