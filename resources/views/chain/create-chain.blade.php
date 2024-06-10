@extends('layout')

<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">


<style>
.draggable {
	font-family: "Trebuchet MS", sans-serif;
	display: flex;
	gap: 30px;
}

.column {
	flex-basis: 20%;
	background: #ddd;
	min-height: 90vh;
	padding: 20px;
	border-radius: 10px;
}

.column h1 {
	text-align: center;
	font-size: 22px;
}

.item {
	background: #fff;
	margin: 20px;
	padding: 20px;
	border-radius: 3px;
	cursor: pointer;
}

.invisible {
	display: none;
}
</style>


@section('main')

<div class='container'>
	<section class=' w-50'>
		<h1 class='pb-5'>Создать цепочку</h1>
		@csrf
		<div class="input-group mb-3">
			<span class="input-group-text" id="basic-addon1">@</span>
			<input type="text" class="form-control p-2" id='title' name='title' placeholder="Название"
				aria-label="Название" aria-describedby="basic-addon1">
		</div>
		<div class="form-check form-switch">
			<input class="form-check-input" type="checkbox" id="start-time-checker">
			<label class="form-check-label" for="flexSwitchCheckDefault">Назначить время начала</label>
		</div>
		<div id='input-start-time' class='d-none'>
			<div class="input-group mb-3 mt-2">
				<span class="input-group-text" id="basic-addon1">⌚</span>
				<input class="form-control" type="time" id='start-time' name="webinar_start_time" value="" />
			</div>
		</div>
		<div class='mt-5 gap-2 d-flex flex-column'>
			<select class="element-select p-2" aria-label="element-selected">
				<option disabled selected>Компоненты</option>
				<option value="1">Сообщение с датой отправки</option>
				<option value="2">Сообщение с паузой</option>
			</select>
			<button id='add-element-btn' type="button" class="btn btn-primary">Добавить</button>
			<div class='d-flex flex-column mb-5 gap-2 p-1' id="elements-container">

			</div>
			<button onclick='submit()' id='submit-btn' type="button" class="btn btn-primary">Сохранить</button>

		</div>
		<div id='time-message' class='d-none gap-2'>
			<input type="text" name="text" />
			<input type="text" id='date-piker' name="date-piker" value="" />
			<button id='remove-item' type="button" class="btn btn-outline-danger">Х</button>
		</div>
	</section>
</div>

@if ($errors-> any())
<div style='background: red'>
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
</div>
@endif
</section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"
	integrity="sha512-zYXldzJsDrNKV+odAwFYiDXV2Cy37cwizT+NkuiPGsa9X1dOz04eHvUWVuxaJ299GvcJT31ug2zO4itXBjFx4w=="
	crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/jquery/1/jquery.min.js">
</script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script>
//const items = document.querySelectorAll('.item')
//const columns = document.querySelectorAll('.column')
const elementSelect = document.querySelector('.element-select')
const addedButton = document.getElementById('add-element-btn')
const startTimeChecker = document.getElementById('start-time-checker')

const messages = [];
let idItemNum = 0
let selectedItem = null;

function addMessageComponentByTime() {
	let elementContainer = document.getElementById('elements-container')
	const messageElement = document.createElement("div");
	messageElement.innerHTML =
		`<div draggable='true' class='list-group-item'>
			<div class='d-flex gap-2 card-body'>
				<div class="input-group-prepend">
					<span class="input-group-text" id="basic-addon1">⇅</span>
				</div>
				<textarea placeholder='Текст' required class="form-control" type="text" name="text"></textarea>
				<select name="dateDispatch">
					<option selected value="today">Сегодня</option>
					<option value="tomorrow">Завтра</option>
				</select>
				<input class="form-control" type='number' name='hour' placeholder='Часы' />
				<input class="form-control" type='number' name='minute' placeholder='Минуты' />
				<button id='remove-item-${idItemNum}' type="button" class="btn btn-outline-danger">Х</button>
			</div>
		</div>`;
	elementContainer.appendChild(messageElement)

	const removeItemButton = document.getElementById(`remove-item-${idItemNum}`)
	removeItemButton.addEventListener('click', function(event) {
		event.target.parentElement.remove();
	})

	idItemNum++;
}

function addMessageComponentByPause() {
	let elementContainer = document.getElementById('elements-container')
	const messageElement = document.createElement("div");
	messageElement.innerHTML =
		`<div draggable='true' class='list-group-item'>
			<div class='d-flex gap-2 card-body'>
				<div class="input-group-prepend">
					<span class="input-group-text" id="basic-addon">⇅</span>
				</div>
				<textarea placeholder='Текст' required class="form-control" type="text" name="text"></textarea>
				<input class="form-control" type='number' name='hour' placeholder='Часы' />
				<input class="form-control" type='number' name='minute' placeholder='Минуты' />
				<input class="form-control" name='second' type='number' placeholder='Секунды' />
				<button id='remove-item-${idItemNum}' type="button" class="btn btn-outline-danger">Х</button>
			</div>
		</div>`;
	elementContainer.appendChild(messageElement)

	const removeItemButton = document.getElementById(`remove-item-${idItemNum}`)
	removeItemButton.addEventListener('click', function(event) {
		event.target.parentElement.remove();
	})

	idItemNum++;
}

elementSelect.addEventListener('change', (event) => {
	selectedItem = event.target.value;
})

startTimeChecker.addEventListener('change', (event) => {
	if (event.target.checked) {
		document.getElementById('input-start-time').classList.remove('d-none')
	} else {
		document.getElementById('input-start-time').classList.add('d-none')
	}
})

addedButton.addEventListener('click', (event) => {
	if (!selectedItem) {
		return;
	}
	switch (selectedItem) {
		case '1':
			addMessageComponentByTime();
			break;
		case '2':
			addMessageComponentByPause();
			break;
	}
	registerDdD()
})

function registerDdD() {
	let dragItem = null;
	const sortableList = document.getElementById('elements-container')

	new Sortable(sortableList, {
		animation: 150,
		ghostClass: 'blue-background-class'
	})
}

function parseInputs() {
	const items = document.querySelectorAll('.list-group-item')
	const inputs = [];
	items.forEach(function(item) {
		const inputsChild = {};
		item.querySelectorAll('input').forEach(function(input) {
			inputsChild[input.name] = input.value;
		});
		item.querySelectorAll('textarea').forEach(function(input) {
			inputsChild[input.name] = input.value;
		});
		item.querySelectorAll('select').forEach(function(input) {
			inputsChild[input.name] = input.value;
		});
		inputs.push(inputsChild);
	})
	return inputs
}

function transformMessage() {
	const inputs = parseInputs();
	const transformed = inputs.map((item, index) => {
		if (!item.dateDispatch) {
			return {
				text: item.text,
				order: index,
				pause: {
					hour: item.hour,
					minute: item.minute,
					second: item.second
				}
			};
		}
		return {
			text: item.text,
			order: index,
			hour: item.hour,
			minute: item.minute,
			dateDispatch: item.dateDispatch
		};
	});
	return transformed;
}

function transformData(timeValue) {
	if (!timeValue) {
		return null;
	}
	const [hour, minute] = timeValue.split(':');
	return {
		hour,
		minute
	}
}

function submit() {
	window.loadingTrue()
	const inputTitle = document.getElementById('title');
	const inputStartTime = document.getElementById('start-time')
	const messages = transformMessage()
	const startTime = inputStartTime.value;
	let transformStartTime = transformData(startTime)
	if (!startTimeChecker.checked) {
		transformStartTime = null
	}

	const title = inputTitle.value;
	if (!title) {
		alert("Пожалуйста, введите заголовок цепочки.");
		return;
	}
	if (!messages.length) {
		alert("Пожалуйста, добавьте элемент цепочки.");
		return;
	}
	const data = {
		title: title,
		webinar_start_time: transformStartTime,
		stages: messages
	};
	fetch('/chain/create', {
		method: 'POST',
		headers: {
			//'Content-Type': 'application/json',
			'Content-Type': 'application/x-www-form-urlencoded',
			"X-CSRF-Token": document.querySelector('input[name=_token]').value
		},
		body: JSON.stringify(data)
	}).then((res) => {
		if (res.status === 200) {
			location.href = "/chain";
		} else {
			alert("Ошибка при создании цепочки");
		}
		window.loadingFalse()
	});
}
</script>
@endsection