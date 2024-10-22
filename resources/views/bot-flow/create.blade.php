@extends('layout')

@section('main')
<div>
	<section class=' w-50'>
		<form onsubmit='window.loadingTrue()' id='create-bot-form' class='mb-3' method='post' action='/bot-flow/create'>
			@csrf

			<h1 class='pb-5'>Создать бота</h1>
			<div class="input-group mb-3">
				<span class="input-group-text" id="basic-addon1">🤖</span>
				<input type="text" class="form-control p-2" id='name' name='name' placeholder="Имя"
					aria-label="Название" aria-describedby="basic-addon1">
			</div>
			<div class="input-group mb-3">
				<span class="input-group-text" id="basic-addon1">✨</span>
				<input type="text" class="form-control p-2" id='token' name='token' placeholder="Токен"
					aria-label="Название" aria-describedby="basic-addon1">
			</div>
			<button class="btn btn-primary">Сохранить</button>
		</form>
		@if ($errors-> any())
		@foreach ($errors->all() as $error)
		<div class="alert alert-danger" role="alert">
			{{$error}}
		</div>
		@endforeach
		@endif
	</section>
</div>
@endsection