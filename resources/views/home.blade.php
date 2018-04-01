@extends('layouts.app')

@section('content')
<div class="container">

    <form action="/pushMessage" method="post">
        <div class="form-group">
            <label for="exampleInputEmail1">Channel ID</label>
            <input type="text" class="form-control" name="channel_id" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Channel ID">
        </div>
        <div class="form-group">
            <label for="exampleFormControlTextarea1">Message</label>
            <textarea class="form-control" id="exampleFormControlTextarea1" name="message" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Push Message</button>
    </form>


    {{--<div class="row justify-content-center">--}}
        {{--<div class="col-md-8">--}}
            {{--<div class="card">--}}
                {{--<div class="card-header">Dashboard</div>--}}

                {{--<div class="card-body">--}}
                    {{--@if (session('status'))--}}
                        {{--<div class="alert alert-success">--}}
                            {{--{{ session('status') }}--}}
                        {{--</div>--}}
                    {{--@endif--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
</div>
@endsection
