@extends('layout')

@section('main')
<div>
	<section class=' w-50'>
		<h1 class='pb-5'>Бот {{$bot->name}}</h1>
		@csrf
		<div class="input-group mb-3">
			<span class="input-group-text" id="basic-addon1">🤖</span>
			<input disabled type="text" value='{{$bot->name}}' required class="form-control p-2" id='name' name='name'
				placeholder="Имя" aria-label="Название" aria-describedby="basic-addon1">
		</div>
		<div class="input-group mb-3">
			<span class="input-group-text" id="basic-addon1">✨</span>
			<input disabled type="text" value='{{$bot->token}}' required class="form-control p-2" id='token'
				name='token' placeholder="Токен" aria-label="Название" aria-describedby="basic-addon1">
		</div>
		<select class="element-select p-2 mt-3" aria-label="element-selected" id='chain-select' name='chain'>
			<option value='#'>Без цепочки</option>
			@foreach ($chains as $chain)
			@if ($chain->id === $bot->chain_model_id)
			<option selected value="{{$chain->id}}">{{$chain->title}}</option>
			@else
			<option value="{{$chain->id}}">{{$chain->title}}</option>
			@endif

			@endforeach
		</select>
		@if (count($triggers))
		<div class='d-flex gap-3 mt-5 mb-2 items-center'>
			<h5 class='mh-25'>Триггеры</h5>
			<!-- <input oninput='triggersSearch({{$triggers}})' type="text" id='search-input' class="form-control p-2"
				placeholder="Поиск" aria-label="Поиск" aria-describedby="basic-addon1"> -->
		</div>
		<table class="table table-hover">
			<tr>
				<th>Выбор</th>
				<th>Триггер</th>
				<th>Ответ</th>
			</tr>
			@foreach($triggers as $trigger)
			@if (in_array($trigger->id, $botIdTriggersArray))
			<tr>
				<th class='align-middle'>
					<input type="checkbox" id="{{$trigger->trigger}}" name="{{$trigger->id}}" checked />
				</th>
				<td class='span2 align-middle'>
					<span class='w-50 d-inline-block text-truncate'>
						{{$trigger->trigger}}
					</span>
				</td>
				<td class='span2 align-middle'>
					<span class='w-50 d-inline-block text-truncate'>
						{{$trigger->text}}
					</span>
				</td>
			</tr>
			@else
			<tr>
				<th class='align-middle'>
					<input type="checkbox" id="{{$trigger->trigger}}" name="{{$trigger->id}}" />
				</th>
				<td class='span2 align-middle'>
					<span class='w-50 d-inline-block text-truncate'>
						{{$trigger->trigger}}
					</span>
				</td>
				<td class='span2 align-middle'>
					<span class='w-50 d-inline-block text-truncate'>
						{{$trigger->text}}
					</span>
				</td>
			</tr>
			@endif
			@endforeach
		</table>
		@endif
		<br />
		<button onclick='window.loadingTrue(), updateBotChain({{$bot->id}}), updateBotTriggers({{$bot->id}})'
			id='submit-btn' type="button" class="btn btn-primary mt-5">Сохранить</button>



	</section>
</div>
<script>
function updateBotChain(botId) {
	const chainSelect = document.getElementById('chain-select');
	let chainId = chainSelect.value;
	if (chainId === '#') {
		chainId = null
	}
	console.log(chainId)
	fetch(`/bot/changeBotChain/${botId}`, {
		method: 'PATCH',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
			"X-CSRF-Token": document.querySelector('input[name=_token]').value
		},
		body: JSON.stringify({
			chainId: chainId
		}),
	}).then((res) => console.log(res))
}

function updateBotTriggers(botId) {
	const selectedCheckboxes = document.querySelectorAll('input[type="checkbox"]:checked');

	const checkTriggers = Array.from(selectedCheckboxes).map((checkbox) => {
		return checkbox.name;
	});

	console.log(checkTriggers)
	location.href = '/'

	fetch(`/bot/updateBotTriggers/${botId}`, {
		method: 'PATCH',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
			"X-CSRF-Token": document.querySelector('input[name=_token]').value
		},
		body: JSON.stringify({
			triggers: checkTriggers
		}),
	}).then((res) => {
		if (res.status === 200) {
			location.href = '/'
		}
	})
}
</script>
@endsection