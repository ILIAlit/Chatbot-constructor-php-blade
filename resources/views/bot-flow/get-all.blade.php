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
		<h1 class='pb-5'>Мои боты (потоки)</h1>
		
		@csrf
		<table class="table table-hover">

			@foreach ($bots as $bot)
			<tr>
				<th class='align-middle'>{{$bot['id']}}</th>
				<td class='span2 align-middle'>
					<span class='w-50 d-inline-block text-truncate'>
						🤖{{$bot['name']}}
					</span>
				</td>
				<td class='span2 align-middle'>
					<button onclick='clickGetFlows({{$bot['id']}})' type="button" class="btn btn-primary"
						data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg"
						data-bot-id="{{$bot['id']}}">Потоки</button>
				</td>
				<td>
					@if (!$bot['id'])
					<button href='#' class="btn btn-primary"
						onclick='clickNotDisableBotButton({{$bot["id"]}})'>Включить</button>
					@else
					<button href='#' class="btn btn-primary"
						onclick='clickDisableBotButton({{$bot["id"]}})'>Выключить</button>
					@endif
				</td>
				<td>
					<button type="button" onclick='clickUpdateButton({{$bot["id"]}});'
						class="btn btn-primary">Изменить</button>
				</td>
				<!--<td>
					<div class="dropdown">
						<button class="btn btn-danger dropdown-toggle" type="button" id="dropdownMenu2"
							data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Danger zone
						</button>
						<div class="dropdown-menu" aria-labelledby="dropdownMenu2">
							<a class="dropdown-item" href='#' onclick='clickDeleteButton({{$bot["id"]}})'
								class="btn btn-danger">Удалить</a>
						</div>
					</div>
				</td>-->
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

const clickGetFlows = (botId) => {
	window.location.href = `/bot-flow/get-all-flow/${botId}`;
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