@extends('layout')

@section('main')
<div class='container'>
	<section>
		<h1 class='pb-5'>Мои боты</h1>
		@csrf
		<table class="table table-hover">
			<tr>
				<th>ID</th>
				<th>Имя</th>
				<th>Активная цепочка</th>
				<th>Активность</th>
				<th>Изменить</th>
				<th>Удалить</th>
			</tr>
			@foreach ($bots as $bot)
			<tr>
				<th class='align-middle'>{{$bot['id']}}</th>
				<td class='span2 align-middle'>
					<span class='w-50 d-inline-block text-truncate'>
						🤖{{$bot['name']}}
					</span>
				</td>
				<td class='span2 align-middle'>
					<span class='w-50 d-inline-block text-truncate'>
						⛓{{$bot['chainName']}}
					</span>
				</td>
				<td class='span2 align-middle'>
					@if ($bot['disable'])
					<button type="button" onclick='clickNotDisableBotButton({{$bot["id"]}})'
						class="btn btn-primary">Включить</button>
					@else
					<button type="button" onclick='clickDisableBotButton({{$bot["id"]}})'
						class="btn btn-danger">Выключить</button>
					@endif
				</td>
				<td>
					<button type="button" onclick='clickUpdateButton({{$bot["id"]}})'
						class="btn btn-primary">Изменить</button>
				</td>
				<td>
					<button type="button" onclick='clickDeleteButton({{$bot["id"]}})'
						class="btn btn-danger">Удалить</button>
				</td>

			</tr>
			@endforeach
		</table>
	</section>
</div>
<script>
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
</script>
@endsection