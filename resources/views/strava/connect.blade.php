@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="col-sm-offset-2 col-sm-8">
			<div class="panel panel-default">
				<div class="panel-heading">
					Connect
				</div>

				<div class="panel-body">
					<!-- Display Validation Errors -->
					@include('common.errors')

						{{ csrf_field() }}

						<!-- Login Button -->
						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-6">
								<a href="{{ $url }}" class="btn btn-default">
									<i class="fa fa-btn fa-sign-in"></i>Connect to Strava
								</a>
							</div>
						</div>

				</div>
			</div>
		</div>
	</div>
@endsection