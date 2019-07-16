@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default panel-table">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col col-xs-6">
                                <h3 class="panel-title">Projects</h3>
                            </div>
                            <div class="col col-xs-6 text-right">
                                <button type="button" class="btn btn-sm btn-primary btn-create">Create New</button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-list">
                            <thead>
                                <tr>
                                    <th><em class="fa fa-cog"></em></th>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>ID</th>
                                    <th>Port</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $key => $project)
                                    <tr>
                                        <td align="center">
                                            <a href="/ui/projects/{{$project['name']}}" class="btn btn-default"><em class="fa fa-info"></em></a>
                                        </td>
                                        <td>{{$key}}</td>
                                        <td>{{$project['name']}}</td>
                                        <td>{{$project['runner']['id']}}</td>
                                        <td>{{$project['runner']['port']}}</td>
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
