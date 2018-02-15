@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default panel-table">
                    <div class="panel-body" style="overflow-x: scroll">
                        <table class="table table-striped table-bordered table-list" style="width: 100%;table-layout: fixed">
                            <colgroup>
                                <col style="width: 3%"/>
                                <col style="width: 10%"/>
                                <col style="width: 7%"/>
                                <col style="width: 5%"/>
                                <col style="width: 75%"/>
                            </colgroup>
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>time</th>
                                <th>code</th>
                                <th>job</th>
                                <th>Message</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($logs as $key => $log)
                                <tr>

                                    <td>{{$key}}</td>
                                    <td>{{(new \Carbon\Carbon($log->Time))->diffForHumans()}}</td>
                                    <td>{{$log->code}}</td>
                                    <td>{{$log->job}}</td>
                                    <td><pre style="width: 100%">{{$log->Message}}</pre></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
