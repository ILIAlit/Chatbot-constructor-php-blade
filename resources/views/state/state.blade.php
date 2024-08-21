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

<style>
.panel {
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	-moz-box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
	-webkit-box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
	box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
	background-color: #fff;
	margin-bottom: 30px;
}

.panel .panel-heading,
.panel .panel-body,
.panel .panel-footer {
	padding-left: 25px;
	padding-right: 25px;
}

.panel .panel-heading {
	padding-top: 20px;
	padding-bottom: 20px;
	position: relative;
}

.panel .panel-heading .panel-title {
	margin: 0;
	font-size: 18px;
	font-weight: 300;
}

.panel .panel-heading button {
	padding: 0;
	margin-left: 5px;
	background-color: transparent;
	border: none;
	outline: none;
}

.panel .panel-heading button i {
	font-size: 14px;
}

.panel .panel-body {
	padding-top: 10px;
	padding-bottom: 15px;
}

.panel .panel-note {
	font-size: 13px;
	line-height: 2.6;
	color: #777777;
}

.panel .panel-note i {
	font-size: 16px;
	margin-right: 3px;
}

.panel .right {
	position: absolute;
	right: 20px;
	top: 32%;
}

.panel.panel-headline .panel-heading {
	border-bottom: none;
}

.panel.panel-headline .panel-heading .panel-title {
	margin-bottom: 8px;
	font-size: 22px;
	font-weight: normal;
}

.panel.panel-headline .panel-heading .panel-subtitle {
	margin-bottom: 0;
	font-size: 14px;
	color: #8d99a8;
}

.panel.panel-scrolling .btn-bottom {
	margin-bottom: 30px;
}

.panel .table>thead>tr>td:first-child,
.panel .table>thead>tr>th:first-child,
.panel .table>tbody>tr>td:first-child,
.panel .table>tbody>tr>th:first-child,
.panel .table>tfoot>tr>td:first-child,
.panel .table>tfoot>tr>th:first-child {
	padding-left: 25px;
}

.panel .table>thead>tr>td:last-child,
.panel .table>thead>tr>th:last-child,
.panel .table>tbody>tr>td:last-child,
.panel .table>tbody>tr>th:last-child,
.panel .table>tfoot>tr>td:last-child,
.panel .table>tfoot>tr>th:last-child {
	padding-left: 25px;
}

.panel-footer {
	background-color: #fafafa;
}

.chart-container {
	transition: 0.3s;
}

.chart-container:hover {
	border: 1px solid #0081c2;
	box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.metric {
	border-radius: 3px;
	padding: 20px;
	margin-bottom: 30px;
	border: 1px solid #DCE6EB;
	transition: 0.3s;
}

.metric:hover {
	border: 1px solid #0081c2;
	box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.metric .icon {
	-webkit-border-radius: 50%;
	-moz-border-radius: 50%;
	border-radius: 50%;
	float: left;
	width: 50px;
	height: 50px;
	line-height: 50px;
	background-color: #0081c2;
	display: flex;
	align-items: center;
	justify-content: center;

}

.metric p {
	margin-bottom: 0;
	line-height: 1.2;
	text-align: right;
}

.metric .number {
	display: block;
	font-size: 28px;
	font-weight: 300;
}

.metric .title {
	font-size: 16px;
}
</style>

@section('main')
<div class='container'>
	<section>
		<h1 class='pb-5'>Статистика</h1>
		@csrf
		<div class=''>

			<div>
				<form method="GET" action="/state-user-create" class="w-100 d-flex gap-2 my-3">
					@csrf
					<select name='bot-selected' class='form-control'>
						<option disabled value="">Бот</option>
						@foreach ($bots as $bot)
						<option value="{{$bot->id}}">{{$bot->name}}</option>
						@endforeach
					</select>
					<input name='date-start' required value="{{$dateStart}}" class='form-control' type='date' />
					<input name='date-stop' required value="{{$dateStop}}" class='form-control' type='date' />
					<button type="submit" class="btn btn-primary">Загрузить</button>
				</form>

			</div>

			<div class="w-100">
				<div class="metric">
					<div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
							fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
							class="lucide lucide-user-round">
							<circle cx="12" cy="8" r="5" />
							<path d="M20 21a8 8 0 0 0-16 0" />
						</svg></div>
					<p>
						<span class="number">{{count($users)}}</span>
						<span class="title">Зарегистрировано с {{$dateStart}} по {{$dateStop}}</span>
					</p>
				</div>
			</div>

			<div class="w-100">
				<div class="metric">
					<div class="icon">


						<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
							stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
							class="lucide lucide-user-round-check">
							<path d="M2 21a8 8 0 0 1 13.292-6" />
							<circle cx="10" cy="8" r="5" />
							<path d="m16 19 2 2 4-4" />
						</svg>
					</div>
					<p>
						<span class="number">{{count($activeUsers)}}</span>
						<span class="title">Активных пользователя</span>
					</p>
				</div>
			</div>
			<div class="w-100" style='display: none'>
				<div class="panel">
					<div class="card chart-container panel-body">
						<canvas id="chart"></canvas>
					</div>
				</div>
			</div>
			<div class="w-100">
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">Активные пользователи</h3>
					</div>
					<div class="panel-body">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Имя</th>
									<th>Фамилия</th>
									<th>Логин TG</th>
									<th>Стадия</th>
									<th>Обновление</th>
									<th>Действие</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($activeUsers as $key => $user)
								<tr>
									<td>{{$key+1}}</td>
									<td>{{$user->name}}</td>
									<td>{{$user->last_name}}</td>
									<td>{{$user->user_name}}</td>
									<td>{{$user->stage}}</td>
									<td>{{$user->ttu}}</td>
									<td>
										<div class="btn-group dropleft">
											<button type="button" class="btn btn-secondary dropdown-toggle"
												data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												Меню
											</button>
											<div class="dropdown-menu p-2">
												@csrf
												<button class="w-100 btn btn-danger"
													onclick='clickUserDeleteButton({{$user->id}})'>Удалить</button>
											</div>
										</div>
					</div>
					</td>
					</tr>
					@endforeach
					</tbody>
					</table>
				</div>
			</div>
		</div>
</div>
</section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js">
</script>

<script>
const clickUserDeleteButton = function(userId) {
	if (window.confirm('Удалить пользователя?')) {
		window.loadingTrue()
		fetch(`/user/delete/${userId}`, {
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