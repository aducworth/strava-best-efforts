{{ csrf_field() }}
<div class="form-group">
	<label for="supportEmail">Email</label>
	<input type="email" name="email"class="form-control" id="supportEmail" placeholder="Email">
</div>
<div class="form-group">
	<label for="supportMessage">Message</label>
	<p class="help-block">Send bugs, questions, or feature requests.</p>
	<textarea class="form-control" id="supportMessage" name="message"></textarea>
</div>
<button type="submit" class="btn btn-default">Send</button>