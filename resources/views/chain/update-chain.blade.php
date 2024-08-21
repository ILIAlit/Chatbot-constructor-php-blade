@extends('layout')

<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"
	integrity="sha512-zYXldzJsDrNKV+odAwFYiDXV2Cy37cwizT+NkuiPGsa9X1dOz04eHvUWVuxaJ299GvcJT31ug2zO4itXBjFx4w=="
	crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/jquery/1/jquery.min.js">
</script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script>
let idItemNum = 0

function secondsConverter(seconds) {
	let hours = Math.floor(seconds / 3600);
	let minutes = Math.floor((seconds % 3600) / 60);
	let remainingSeconds = seconds % 60;
	return {
		hours: hours,
		minutes: minutes,
		seconds: remainingSeconds
	}
}

function registerDdD() {
	let dragItem = null;
	const sortableList = document.getElementById('elements-container')

	new Sortable(sortableList, {
		animation: 150,
		ghostClass: 'blue-background-class'
	})
}

function addMessageComponentByTime(text = null, hour = null, minute = null, dayDispatch = null, file = null) {
	let elementContainer = document.getElementById('elements-container')
	const messageElement = document.createElement("div");
	messageElement.innerHTML =
		`<div draggable='true' class='list-group-item'>
			<div class='d-flex card-body gap-1'>
				<div class="input-group-prepend">
					<span class="input-group-text" id="basic-addon1">⇅</span>
				</div>
				<div class='d-flex flex-column gap-1 w-100'>
				    <div class='d-flex gap-2'>
						<textarea placeholder='Текст' required class="form-control" type="text" name="text" value="${text ? text : ''}">${text ? text : ''}</textarea>
						<select class="form-control" name="dayDispatch" id="dayDispatch-${idItemNum}">
							<option selected value="0">В это же день</option>
							<option value="1">Через день</option>
							<option value="2">Через 2 дня</option>
							
						</select>
						<div class='w-50 d-flex gap-2'>
						<input class="form-control" value="${hour}" type='number' name='hour' placeholder='Часы' />
						<input class="form-control" type='number' value="${minute}" name='minute' placeholder='Минуты' />
						<input class="form-control" type='number' value="0" name='second' disabled placeholder='Секунды' />
						</div>
					</div>
					<div id='time-file-input-${idItemNum}'>
						
					</div>
					
				</div>
				
				<button id='remove-item-${idItemNum}' type="button" class="btn btn-outline-danger">Х</button>
			</div>
		</div>`


	elementContainer.appendChild(messageElement)
	const selectElement = document.getElementById(`dayDispatch-${idItemNum}`)


	selectElement.querySelectorAll('option').forEach(function(option) {

		if (option.value === dayDispatch) {
			option.selected = true;
		}
	});

	const timeFileInput = document.getElementById(`time-file-input-${idItemNum}`)
	if (file) {
		timeFileInput.innerHTML =
			`<button class="btn btn-primary" id="edit-file-btn-${idItemNum}">Изменить файл</button> <input style="display: none;" type='file' edit="no" class='form-control' name='file' accept="" /> <input style="display: none;" type='text' value='${file}' name='src' accept="" />`

		const editFileBtn = document.getElementById(`edit-file-btn-${idItemNum}`)
		editFileBtn.addEventListener('click', function(event) {
			timeFileInput.innerHTML =
				`<input type='file' edit="yes" class='form-control' name='file' accept="" />`
		})
	} else {

		timeFileInput.innerHTML = `<input type='file' edit="yes" class='form-control' name='file' accept="" />`
	}




	const removeItemButton = document.getElementById(`remove-item-${idItemNum}`)
	removeItemButton.addEventListener('click', function(event) {
		const item = this.closest('.list-group-item');
		item.remove();
	})
	registerDdD()
	idItemNum++;
}

function addMessageComponentByPause(text = null, seconds = null, id) {
	if (seconds) {
		timeObject = secondsConverter(seconds)
	}
	let elementContainer = document.getElementById('elements-container')
	const messageElement = document.createElement("div");
	messageElement.innerHTML =
		`<div draggable='true' class='list-group-item'>
			<div class='d-flex gap-2 card-body'>
				<div class="input-group-prepend">
					<span class="input-group-text" id="basic-addon">⇅</span>
				</div>
				<textarea placeholder='Текст' required class="form-control" type="text" name="text" value="${text ? text : ''}">${text ? text : ''}</textarea>
				<input value="${seconds ? timeObject.hours : ''}" class="form-control" type='number' name='hour' placeholder='Часы' />
				<input value="${seconds ? timeObject.minutes : ''}" class="form-control" type='number' name='minute' placeholder='Минуты' />
				<input value="${seconds ? timeObject.seconds : ''}"  class="form-control" name='second' type='number' placeholder='Секунды' />
				<button id='remove-item-${idItemNum}' type="button" class="btn btn-outline-danger">Х</button>
				
			</div>
		</div>`;
	elementContainer.appendChild(messageElement)

	const removeItemButton = document.getElementById(`remove-item-${idItemNum}`)
	removeItemButton.addEventListener('click', function(event) {
		const item = this.closest('.list-group-item');
		item.remove();
	})
	registerDdD()
	idItemNum++;
}
</script>

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
	<section class=' w-100'>
		<h1 class='pb-5'>Изменить цепочку</h1>
		@csrf
		<div class="input-group mb-3">
			<span class="input-group-text" id="basic-addon1">@</span>
			<input value='{{$chain->title}}' type="text" class="form-control p-2" id='title' name='title'
				placeholder="Название" aria-label="Название" aria-describedby="basic-addon1">
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
				@foreach ($stages as $stage)
				@if (isset($stage->pause))
				<script>
				addMessageComponentByPause(`{{$stage->text}}`, "{{$stage->pause}}", `{{$stage->id}}`)
				</script>
				@else

				<script>
				addMessageComponentByTime(`{{$stage->text}}`, "{{$stage->hour}}", "{{$stage->minute}}", "{{$stage->day_dispatch}}",
					"{{$stage->file_src}}")
				</script>
				@endif

				@endforeach
			</div>
			<button onclick='submit({{$chain->id}})' id='submit-btn' type="button"
				class="btn btn-primary">Сохранить</button>

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

<script>
//const items = document.querySelectorAll('.item')
//const columns = document.querySelectorAll('.column')
const elementSelect = document.querySelector('.element-select')
const addedButton = document.getElementById('add-element-btn')
const startTimeChecker = document.getElementById('start-time-checker')

const messages = [];
let selectedItem = null;

elementSelect.addEventListener('change', (event) => {
	selectedItem = event.target.value;
})

//
function formatTime(time) {
	let [hours, minutes] = time.split(':');


	hours = hours.padStart(2, '0');
	minutes = minutes.padStart(2, '0');

	return `${hours}:${minutes}`;
}

function checkedTimeChecker(hour, minute) {
	if (hour && minute) {
		startTimeChecker.checked = true
		document.getElementById('input-start-time').classList.remove('d-none')
		document.getElementById('start-time').value = formatTime(`${hour}:${minute}`)
		return true;
	}
	return false;
}
checkedTimeChecker("{{$chain->hour}}", "{{$chain->minute}}")

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
})

function parseInputs() {
	const items = document.querySelectorAll('.list-group-item')
	const inputs = [];
	items.forEach(function(item) {
		const inputsChild = {};
		item.querySelectorAll('input').forEach(function(input) {
			if (input.type === 'file') {
				inputsChild[input.name] = input.files[0];
				inputsChild['fileEdit'] = input.getAttribute('edit');
			} else {
				inputsChild[input.name] = input.value;
			}
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
	console.log(inputs)
	const transformed = inputs.map((item, index) => {
		if (!item.dayDispatch) {
			return {
				text: item.text,
				order: index,
				hour: item.hour,
				minute: item.minute,
				second: item.second
			};
		}
		return {
			text: item.text,
			order: index,
			hour: item.hour,
			minute: item.minute,
			dayDispatch: item.dayDispatch,
			file: item.file,
			fileEdit: item.fileEdit,
			src: item.src
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

function submit(chainId) {
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
		webinar_start_time: JSON.stringify(transformStartTime),
		stages: messages
	};



	const formData = new FormData();
	for (let key in data) {
		if (key === 'stages') {
			data[key].forEach((item, index) => {
				for (let prop in item) {
					formData.append(`stages[${index}][${prop}]`, item[prop]);
				}
			});
		} else {
			formData.append(key, data[key]);
		}
	}

	fetch(`/chain/update-chain/${chainId}`, {
		method: 'POST',
		headers: {
			'X-CSRF-TOKEN': '{{ csrf_token() }}',
		},
		body: formData
	}).then((res) => {
		if (res.status === 200) {
			location.href = "/chain";
		} else {
			alert("Ошибка при обновлении цепочки");
		}
		window.loadingFalse()
	});
}
</script>
@endsection