<?php

namespace App;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $dates = ['last_universalis_pull',
                        'last_market_update'];

    public function refreshPricing()
    {
        $guzzle = new Client([
            'base_uri' => 'https://universalis.app/api/'
        ]);

        $response = $guzzle->get('https://universalis.app/api/Hyperion/'.$this->id);

        $itemListings = json_decode($response->getBody(), true);

        //dd($itemListings);

        // https://universalis.app/api/history/Hyperion/27837

        $response = $guzzle->get('https://universalis.app/api/history/Hyperion/'.$this->id);

        $itemHistory = json_decode($response->getBody(), true);

        //dd($itemHistory);

        $this->current_price         = $this->calculateCurrentPrice($itemListings);
        $this->stack_size            = $this->calculateStackSize($itemListings);
        $this->last_market_update    = $this->extractLastMarketUpdate($itemListings);

        $oneWeekAgo = Carbon::now()->subWeek()->timestamp;

        $this->historical_price      = $this->calculateHistoricalPrice($itemHistory, $oneWeekAgo);
        $this->historical_stack_size = $this->calculateHistoricalStackSize($itemHistory, $oneWeekAgo);
        $this->sell_speed            = $this->calculateSellSpeed($itemHistory, $oneWeekAgo);

        $this->current_vpt           = $this->calculateCurrentVPT();
        $this->historical_vpt        = $this->calculateHistoricalVPT();

        $this->last_universalis_pull = Carbon::now();
        $this->last_universalis_pull->setTimezone('UTC');

        $this->save();

        return true;
    }

    protected function calculateCurrentPrice($itemListings)
    {
        $maxPriceConsideration = ($itemListings['averagePrice'] * 5);

        $total = 0;
        $count = 0;

        foreach($itemListings['listings'] as $listing) {
            if($count < 10 && $listing['hq'] == false && $listing['pricePerUnit'] <= $maxPriceConsideration) {
                $total += $listing['pricePerUnit'];
                $count++;
            }
        }

        if ($count != 0) {
            $current_price = (int)($total / $count);

            return $current_price;
        }


        return 0;
    }

    protected function calculateStackSize($itemListings)
    {
        $total = 0;
        $count = 0;

        foreach($itemListings['listings'] as $listing) {
            if($count < 10 && $listing['hq'] == false) {
                $total += $listing['quantity'];
                $count++;
            }
        }


        if ($count != 0) {
            $stack_size = (int)($total / $count);

            return $stack_size;
        }

        return 0;
    }

    protected function extractLastMarketUpdate($itemListings)
    {
        $lastUpdate = (int) ($itemListings['lastUploadTime'] / 1000);
        $lastUpdate = Carbon::createFromTimestamp($lastUpdate);
        $lastUpdate->setTimezone('UTC');

        return $lastUpdate;
    }

    protected function calculateHistoricalPrice($itemHistory, $oneWeekAgo)
    {
        $total = 0;
        $count = 0;

        foreach ($itemHistory['entries'] as $entry) {
            if ($entry['timestamp'] >= $oneWeekAgo && $entry['hq'] == false) {
                $total += $entry['pricePerUnit'];
                $count++;
            }
        }

        if ($count != 0) {
            $historical_price = (int)($total / $count);

            return $historical_price;
        }

        return 0;
    }

    protected function calculateHistoricalStackSize($itemHistory, $oneWeekAgo)
    {
        $total = 0;
        $count = 0;

        foreach($itemHistory['entries'] as $entry) {
            if($entry['timestamp'] >= $oneWeekAgo && isset($entry['quantity']) && $entry['hq'] == false) {
                $total += $entry['quantity'];
                $count++;
            }
        }

        if ($count != 0) {
            $historical_stack_size = (int)($total / $count);

            return $historical_stack_size;
        }

        return 0;
    }

    // total nq historical quantity sold, up to past week. if full past week isn't available, extrapolate what a week would look like based on available time period

    protected function calculateSellSpeed($itemHistory, $oneWeekAgo)
    {
        $total = 0;

        foreach($itemHistory['entries'] as $entry) {
            if($entry['timestamp'] >= $oneWeekAgo && isset($entry['quantity']) && $entry['hq'] == false) {
                $total += $entry['quantity'];
            }
        }

        $daily = (int) ($total / 7);

        return $daily;
    }

    protected function calculateCurrentVPT()
    {
        $current_vpt = $this->current_price * $this->sell_speed;

        return $current_vpt;
    }

    protected function calculateHistoricalVPT()
    {
        $historical_vpt = $this->historical_price * $this->sell_speed;

        return $historical_vpt;
    }
}
