@extends('layout')

@section('main')
<div class='container'>
	<section class='w-50'>
		<h1 class='pb-5'>Логи</h1>
		<br />
		<textarea>{{$logs}}</textarea>
	</section>

</div>

<script type="text/javascript" src="//cdn.jsdelivr.net/jquery/1/jquery.min.js">
</script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
@endsection