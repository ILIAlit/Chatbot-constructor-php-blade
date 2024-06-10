@extends('layout')

@section('main')
<div class='container'>
	<section class='w-50'>
		<h1 class='pb-5'>Мои триггеры</h1>
		<button type="button" class="btn btn-primary" data-bs-toggle="modal"
			data-bs-target=".bd-example-modal-lg">Создать триггер</button>
		<br />
		<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
			aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content p-4">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Создать триггер</h5>
					</div>
					<form onsubmit='window.loadingTrue()' method='post' action='/bot-constructor/trigger/create'>
						@csrf
						<div class="input-group mb-3">
							<span class="input-group-text" id="basic-addon1">🌟</span>
							<input type="text" required class="form-control p-2" id='trigger' name='trigger'
								placeholder="Триггер" aria-label="Триггер" aria-describedby="basic-addon1">
						</div>
						<div class="input-group mb-3">
							<span class="input-group-text" id="basic-addon1">📞</span>
							<input type="text" required class="form-control p-2" id='text' name='text'
								placeholder="Ответ" aria-label="Текст" aria-describedby="basic-addon1">
						</div>
						<div class="modal-footer">
							<button class="btn btn-primary">Сохранить</button>
						</div>
						@if ($errors-> any())
						@foreach ($errors->all() as $error)
						<div class="alert alert-danger" role="alert">
							{{$error}}
						</div>
						@endforeach
						@endif
					</form>
				</div>
			</div>
		</div>
		@if ($errors-> any())
		@foreach ($errors->all() as $error)
		<div class="alert alert-danger" role="alert">
			{{$error}}
		</div>
		@endforeach
		@endif
		<div class='pt-5 d-flex gap-2'>
			@foreach ($triggers as $trigger)
			<button type="button" class="btn btn-primary">
				{{$trigger->trigger}}<span class="badge badge-light">x</span>
			</button>
			@endforeach
		</div>
	</section>
</div>

<script type="text/javascript" src="//cdn.jsdelivr.net/jquery/1/jquery.min.js">
</script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type='module'>
$('#exampleModal').on('show.bs.modal', function(event) {
	var button = $(event.relatedTarget) // Button that triggered the modal
	var recipient = button.data('whatever') // Extract info from data-* attributes
	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
	var modal = $(this)
	modal.find('.modal-title').text('New message to ' + recipient)
	modal.find('.modal-body input').val(recipient)
})
</script>
@endsection