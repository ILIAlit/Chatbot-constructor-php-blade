<?php

class StageDto {
	public function __construct (
		private string $text,
		private int $pause
	) {}
}