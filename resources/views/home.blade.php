@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Data Updates</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @include('flash::message')

                    <form method="post" action="/update">
                        @csrf

                        <p>
                            <input type="radio" name="update_type" id="update_items" value="update_items"> Update Items (Last Updated {{ $updates['items'] }})<br>
                            <input type="radio" name="update_type" id="update_marketable" value="update_marketable"> Update Marketable Items (Last Updated {{ $updates['marketable'] }})<br>
                            <input type="radio" name="update_type" id="update_marketable" value="update_gatherable"> Update Gatherable Items (Last Updated {{ $updates['gatherable'] }})
                        </p>

                        <button type="submit" class="btn btn-success btn-block" name="do_update" value="do_update">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
