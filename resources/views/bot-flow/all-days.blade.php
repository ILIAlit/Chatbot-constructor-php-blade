@extends('layout')

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
	integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
	integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
	integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
	integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
</script>


@section('main')
<div class='container'>
	<section>
		<h1 class='pb-5'>Дни</h1>
		<button onclick='' type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
			data-bs-target=".bd-example-modal-lg">Добавить день</button>
		@csrf
		<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
			aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content p-4">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Создать день</h5>
					</div>
					<form onsubmit='window.loadingTrue()' method='post' action='/flow-days/create'>
						@csrf
						<div class="input-group mb-3">
							<span class="input-group-text" id="basic-addon1">🌟</span>
							<input value='{{$flowId}}' type="number" required name='flowId' hidden>
							<input type="number" required class="form-control p-2" id='number' name='number'
								placeholder="Номер дня" aria-label="Триггер" aria-describedby="basic-addon1">
						</div>
						<div class="modal-footer">
							<button class="btn btn-primary">Создать</button>
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
		<table class="table table-hover">

			@foreach ($days as $day)
			<tr>
				<th class='align-middle'>{{$day['id']}}</th>
				<td class='span2 align-middle'>
					<span class='w-50 d-inline-block text-truncate'>
						Номер дня - {{$day['number']}}
					</span>
				</td>
				<td class='span2 align-middle'>
					<span class='w-50 d-inline-block text-truncate'>
						{{$day['text']}}
					</span>
				</td>
				<td>
					<button type="button" onclick='clickUpdateButton({{$day["id"]}});'
						class="btn btn-primary">Изменить</button>
				</td>
			</tr>
			@endforeach
		</table>
	</section>
</div>
<script>
const createMailingFormAction = (botId) => {
	const modalForm = document.getElementById('modal-mailing-form');
	modalForm.action = '/bot/make-mailing/' + botId;
	console.log(modalForm.action)
}

const setUserCountMailForm = (userCount) => {
	const userCountTag = document.getElementById('user-count-mail-form');
	userCountTag.textContent = 'Получателей: ' + userCount;
	console.log(userCount)
}

const clickDisableBotButton = (botId) => {
	window.loadingTrue()
	fetch(`/bot/disable/${botId}`, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
			"X-CSRF-Token": document.querySelector('input[name=_token]').value
		},
	}).then((res) => {
		if (res.status === 200) {
			location.reload()
		}
	}).finally(() => {
		window.loadingFalse()
		location.reload()
	})
}

const clickNotDisableBotButton = (botId) => {
	window.loadingTrue()
	fetch(`/bot/not-disable/${botId}`, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
			"X-CSRF-Token": document.querySelector('input[name=_token]').value
		},
	}).then((res) => {
		if (res.status === 200) {
			location.reload()
		}
	}).finally(() => {
		window.loadingFalse()
		location.reload()
	})
}

const clickUpdateButton = (botId) => {
	window.location.href = `/bot/update-bot/${botId}`;
}

const clickDeleteButton = function(botId) {
	if (window.confirm('Удалить бота?')) {
		window.loadingTrue()
		fetch(`/bot/delete-bot/${botId}`, {
			method: 'DELETE',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				"X-CSRF-Token": document.querySelector('input[name=_token]').value
			},
		}).then((res) => {
			if (res.status === 200) {
				location.reload()
			}
		}).finally(() => {
			window.loadingFalse()
		})
	}


}
</script>
@endsection