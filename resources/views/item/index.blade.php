@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">

            <div class="card">
                <div class="card-header">Items</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @include('flash::message')

                    <form method="post" action="/item">
                        @csrf

                        <p>
                            <button type="submit" class="btn btn-primary btn-block" name="refresh_pricing" value="refresh_all">Refresh All Items</button><br>
							<span style="color: red; font-size: 3em;">Remember to run jobs!</span>
                        </p>

                        <p>
                            <label for="filter_min_price">Minimum Current Price: <input type="text" name="filter_min_price" id="filter_min_price" value="{{ $filter['min_price'] }}"></label>
                            <label for="filter_min_speed">Minimum Sell Speed: <input type="text" name="filter_min_speed" id="filter_min_speed" value="{{ $filter['min_speed'] }}"></label>
                            <button type="submit" class="btn btn-primary" name="refresh_pricing" value="apply_filters">Apply Filters</button>
                        </p>

                        <div class="table-responsive">
                            <table id="item_table" class="table table-striped table-bordered table-hover nowrap">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">ID</th>
                                        <th class="text-nowrap">Item</th>
                                        <th class="text-nowrap text-center">Current Price</th>
                                        <th class="text-nowrap text-center">Historical Price</th>
                                        <th class="text-nowrap text-center">Stack Size</th>
                                        <th class="text-nowrap text-center">Historical Stack Size</th>
                                        <th class="text-nowrap text-center">Sell Speed</th>
                                        <th class="text-nowrap text-center">Historical VPT</th>
                                        <th class="text-nowrap text-center">Current VPT</th>
                                        <th class="text-nowrap text-center">Last Universalis Pull</th>
                                        <th class="text-nowrap text-center">Last Market Update</th>
                                        <th class="text-nowrap text-center">Refresh Prices</th>
                                    </tr>
                                </thead>
                                <tbody>

                                @foreach ($items as $item)
                                <tr>
                                    <td class="text-nowrap">{{ $item->id }}</td>
                                    <td class="text-nowrap">{{ $item->name }}</td>
                                    <td class="text-nowrap text-center">
                                        @if (is_null($item->current_price))
                                        &mdash;
                                        @else
                                            {{ $item->current_price }}
                                        @endif
                                    </td>
                                    <td class="text-nowrap text-center">
                                        @if (is_null($item->historical_price))
                                        &mdash;
                                        @else
                                            {{ $item->historical_price }}
                                        @endif
                                    </td>
                                    <td class="text-nowrap text-center">
                                        @if (is_null($item->stack_size))
                                        &mdash;
                                        @else
                                            {{ $item->stack_size }}
                                        @endif
                                    </td>
                                    <td class="text-nowrap text-center">
                                        @if (is_null($item->historical_stack_size))
                                        &mdash;
                                        @else
                                            {{ $item->historical_stack_size }}
                                        @endif
                                    </td>
                                    <td class="text-nowrap text-center">
                                        @if (is_null($item->sell_speed))
                                        &mdash;
                                        @else
                                            {{ $item->sell_speed }}
                                        @endif
                                    </td>
                                    <td class="text-nowrap text-center">
                                        @if (is_null($item->historical_vpt))
                                        &mdash;
                                        @else
                                            {{ $item->historical_vpt }}
                                        @endif
                                    </td>
                                    <td class="text-nowrap text-center">
                                        @if (is_null($item->current_vpt))
                                        &mdash;
                                        @else
                                            {{ $item->current_vpt }}
                                        @endif
                                    </td>
                                    <td class="text-nowrap text-center">
                                        @if (is_null($item->last_universalis_pull))
                                        &mdash;
                                        @else
                                            {{ $item->last_universalis_pull->setTimezone('America/Chicago')->toDateTimeString() }}
                                        @endif
                                    </td>
                                    <td class="text-nowrap text-center">
                                        @if (is_null($item->last_market_update))
                                        &mdash;
                                        @else
                                            {{ $item->last_market_update->setTimezone('America/Chicago')->toDateTimeString() }}
                                        @endif
                                    </td>
                                    <td class="text-nowrap text-center">
                                        <button type="submit" class="btn btn-primary btn-block" name="refresh_pricing" value="{{ $item->id }}">Refresh</button>
                                    </td>
                                </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
@endsection
