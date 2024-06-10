<?php

class ResponsePause {
	public function __construct (
		private int $hour,
		private int $minute,
        private int $second,
	) {}
}