<?php

namespace App\Http\Controllers;

use App\Item;
use App\Jobs\RefreshItem;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    protected $filters = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $filter['min_price'] = isset($this->filters['min_price']) ? $this->filters['min_price'] : 300;
        $filter['min_speed'] = isset($this->filters['min_speed']) ? $this->filters['min_speed'] : 200;

        $items = Item::where('marketable', true)
                     ->where('gatherable', true)
                     ->where('current_price', '>=', $filter['min_price'])
                     ->where('sell_speed', '>', $filter['min_speed'])
                     ->orderBy('name', 'asc')
                     ->get();

        return view('item.index', compact('items', 'filter'));
    }

    public function refresh(Request $request)
    {
        $refresh = $request->refresh_pricing;

        if($refresh == 'refresh_all') {
            $this->startRefreshAll();

            flash()->success("Items' market data will begin refreshing in the background.")->important();

            return redirect()->route('item.index');
        }

        if($refresh == 'apply_filters') {
            $this->filters['min_price'] = $request->filter_min_price;
            $this->filters['min_speed'] = $request->filter_min_speed;

            flash()->success("Pricing filters applied.")->important();

            return $this->index();
        }

        $item = Item::find($refresh);

        if($item->refreshPricing()) {
            flash()->success("The item's market data has been refreshed.")->important();
        }

        return redirect()->route('item.index');
    }

    protected function startRefreshAll()
    {
        $items = Item::where('marketable', true)
                     ->where('gatherable', true)
                     ->orderBy('name', 'asc')
                     ->get();

        foreach($items as $item) {
            RefreshItem::dispatch($item);
        }
    }
}
