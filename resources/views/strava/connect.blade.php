@extends('layouts.app')

@section('content')

	<div class="page-header">
	  <h1>Connect with Strava</h1>
	</div>
	<div class="panel">
           
            <div class="panel-body">
	            
					<!-- Display Validation Errors -->
					@include('common.errors')

						{{ csrf_field() }}

						<!-- Login Button -->
						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-6">
								
								<ul>
									<li>See a list of all of your best efforts</li>
									<li>Import weather conditions for your runs</li>
									<li>Compare splits from runs</li>
									<li>Set annual distance goals</li>
								</ul>
								
								<a href="{{ $url }}" class="btn">
									<img src="{{ asset("assets/images/ConnectWithStrava.png") }}"/>
								</a>
							</div>
						</div>
						
            </div>
            
	</div>

@endsection