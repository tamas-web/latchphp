<?php

namespace App\Html\Elements\Interfaces;

interface ElementInterface {

	public static function Create();

	public function buffer();

	public function render();

}
