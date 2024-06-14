<?php

namespace App\Services;

class FileServices {
	public function saveFile($file): string {
		return $file->store('chain-files');
    }

	public function generateLink($storePath) {
		if(!$storePath) {
			return null;
		}
		return $storePath;
	}
}