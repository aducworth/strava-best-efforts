@extends('layouts.app')

@section('content')

    <!-- Current Activities -->
    @if (count($activities) > 0)
    	<div class="page-header">
		  <h1>Activities <small>{{ count($activities) }}</small></h1>
		</div>
        <div class="panel panel-default">
	        
           
            <div class="panel-body">
                <table class="table table-striped activity-table">

                    <!-- Table Headings -->
                    <thead>
                        <th>Activity</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>&nbsp;</th>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        @foreach ($activities as $activity)
                            <tr>
                                <td class="table-text">
                                    <div>{{ $activity->name }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ $activity->type }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ $activity->start_date_local }}</div>
                                </td>

                                <td>
                                    <!-- TODO: Delete Button -->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
	
@endsection