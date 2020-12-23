<?php

namespace App\Http\Controllers;

use App\Item;
use App\Update;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
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
        $updates = [];

        $updates['items']      = Update::firstWhere('type', 'items');
        $updates['marketable'] = Update::firstWhere('type', 'marketable');
        $updates['gatherable'] = Update::firstWhere('type', 'gatherable');

        if(!empty($updates['items']->updated_at) && $updates['items']->updated_at instanceof Carbon) {
            $updates['items'] = $updates['items']->updated_at->diffForHumans();
        } else {
            $updates['items'] = 'unknown';
        }

        if(!empty($updates['marketable']->updated_at) && $updates['marketable']->updated_at instanceof Carbon) {
            $updates['marketable'] = $updates['marketable']->updated_at->diffForHumans();
        } else {
            $updates['marketable'] = 'unknown';
        }

        if(!empty($updates['gatherable']->updated_at) && $updates['gatherable']->updated_at instanceof Carbon) {
            $updates['gatherable'] = $updates['gatherable']->updated_at->diffForHumans();
        } else {
            $updates['gatherable'] = 'unknown';
        }

        return view('home', compact('updates'));
    }

    /**
     * Perform the requested update
     */
    public function update(Request $request)
    {
        $update = $request->update_type;

        switch ($update) {
            case 'update_items':
                $this->doUpdateItems();
                break;
            case 'update_marketable':
                $this->doUpdateMarketable();
                break;
            case 'update_gatherable':
                $this->doUpdateGatherable();
                break;
            default:
                dd('default?');
                break;
        }

        return redirect()->route('update.index');
    }

    protected function doUpdateItems()
    {
        $guzzle = new Client([
            'base_uri' => 'https://github.com/'
        ]);

        $response = $guzzle->get('https://github.com/ffxiv-teamcraft/ffxiv-teamcraft/raw/staging/apps/client/src/assets/data/items.json');

        $items = json_decode($response->getBody(), true);

        foreach($items as $key => $item) {
            DB::table('items')->insertOrIgnore ([
                ['id'   => $key,
                 'name' => $item['en']]
            ]);
        }

        $update = Update::firstWhere('type', 'items');

        $update->touch();

        flash()->success('The item IDs and names have been updated.')->important();
    }

    protected function doUpdateMarketable()
    {
        $guzzle = new Client([
            'base_uri' => 'https://github.com/'
        ]);

        $response = $guzzle->get('https://raw.githubusercontent.com/ffxiv-teamcraft/ffxiv-teamcraft/staging/apps/client/src/assets/data/market-items.json');

        // Clear current marketable items first
        $items = Item::where('marketable', true)->get();

        foreach($items as $item) {
            $item->marketable = false;
            $item->save();
        }

        // Now we can update the marketable list
        $items = json_decode($response->getBody(), true);

        foreach($items as $itemID) {
            $item = Item::find($itemID);
            $item->marketable = true;
            $item->save();
        }

        $update = Update::firstWhere('type', 'marketable');

        $update->touch();

        flash()->success('The marketable items have been updated.')->important();
    }

    protected function doUpdateGatherable()
    {
        $guzzle = new Client([
            'base_uri' => 'https://github.com/'
        ]);

        $response = $guzzle->get('https://raw.githubusercontent.com/ffxiv-teamcraft/ffxiv-teamcraft/staging/apps/client/src/assets/data/gathering-log-pages.json');

        // Clear current gatherable items first
        $items = Item::where('gatherable', true)->get();

        foreach($items as $item) {
            $item->gatherable = false;
            $item->save();
        }

        // Now we can update the gatherable list
        $gatherable = json_decode($response->getBody(), true);

        foreach($gatherable as $typeKey => $type) {
            foreach($type as $pageKey => $page) {
                foreach($page['items'] as $item) {
                    $item = Item::find($item['itemId']);
                    $item->gatherable = true;
                    $item->save();
                }
            }
        }

        $update = Update::firstWhere('type', 'gatherable');

        $update->touch();

        flash()->success('The gatherable items have been updated.')->important();
    }
}
