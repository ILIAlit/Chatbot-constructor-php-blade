@extends('layout')

@section('main')
<div class='container'>
	<h1 class='pb-5'>Мои цепочки</h1>
	@csrf
	<table class="table">
		<tr>
			<th>ID</th>
			<th>Заголовок</th>
			<th>Изменить</th>
			<th>Удалить</th>
		</tr>
		@foreach ($chains as $chain)
		<tr>
			<td>{{$chain->id}}</td>
			<td class='span2'>
				<span class='w-50 d-inline-block text-truncate'>
					{{$chain->title}}
					@if ($chain->webinar_start_time)
					/⌚ - {{$chain->webinar_start_time}}
					@endif
				</span>
			</td>
			<td>
				<button type="button" onclick='clickUpdateButton({{$chain->id}})'
					class="btn btn-primary">Изменить</button>
			</td>
			<td>
				<button type="button" onclick='clickDeleteButton({{$chain->id}})'
					class="btn btn-danger">Удалить</button>
			</td>
		</tr>
		@endforeach
	</table>
</div>
<script>
const clickUpdateButton = (botId) => {
	window.location.href = `/chain/update-chain/${botId}`;
}

const clickDeleteButton = (chainId) => {
	window.loadingTrue()
	fetch(`/chain/delete-chain/${chainId}`, {
		method: 'DELETE',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
			"X-CSRF-Token": document.querySelector('input[name=_token]').value
		},
	}).then((res) => {
		if (res.status === 200) {
			location.reload()
		}
		window.loadingFalse()
	})
}
</script>
@endsection