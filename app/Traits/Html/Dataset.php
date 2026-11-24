<?php

namespace App\Traits\Html;


trait Dataset {

	protected $dataset = [];

	public function dataset( array $dataset ) : self {

		$this->dataset = $dataset;

		return $this;

	}

	public function getDataset() : array {

		return $this->dataset;

	}

	public function getDatasetSafe() : string {

		$dataset = [];

		foreach( $this->dataset as $key => $value ){

			if( is_string( $value ) ){

				$dataset[] = sprintf( 'data-%s="%s"', esc_html( $key ), esc_html( $value ) );

			} else {

				$dataset[] = sprintf( 'data-%s="%s"', esc_html( $key ), esc_html( json_encode( $value ) ) );

			}

		}

		$dataset = join( ' ', $dataset );

		return $dataset ? ' ' . $dataset : '';

	}

}
