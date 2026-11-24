<?php

namespace App\Html;

use App\Html\Elements\Interfaces\ElementInterface;
use App\Html\Elements\{ A, Button, Div, Form, Fieldset, Input, Label, Legend, P, Select, Textarea, Ul, Li };
use App\Http\URL;
use App\Views\View;
use ReflectionObject;

class Builder implements ElementInterface {

	const DEFINED_FORM_ELEMENTS = [
		'a'                => A::Class,
		'button'           => Button::Class,
		'div'              => Div::Class,
		'fieldset'         => Fieldset::Class,
		'form'             => Form::Class,
		'input'            => Input::Class,
		'input::icheck'    => Input::Class,
		'input::iradio'    => Input::Class,
		'input::icongroup' => Input::Class,
		'label'            => Label::Class,
		'legend'           => Legend::Class,
		'p'                => P::Class,
		'select'           => Select::Class,
		'ul'               => Ul::class,
		'li'               => Li::class,
		'textarea'         => Textarea::Class,
		'raw'              => 'raw',
	];

	protected string $html = '';

	protected array $fields = [];

	protected function __construct( array $fields ){

		$this->fields = $fields;

		$this->html = $this->buildForm( $this->fields );

	}

	public static function Create( array $fields = [] ) : self {

		return new self( $fields );

	}

	public static function Generate( array $fields ) : void {

		( new self( $fields ) )->render();

	}

	protected function buildForm( array $fields ){

		$html = '';

		foreach( $fields as $field ){

			if( ! isset( $field['elementType'] ) ){

				continue;

			}

			$field['elementType'] = strtolower( $field['elementType'] );

			$elementType = self::DEFINED_FORM_ELEMENTS[ $field['elementType'] ] ?? false;

			if( ! $elementType ){

				continue;

			}

			if( 'raw' === $elementType ){

				$html .= $field['html'] ?? null;

				continue;

			} elseif( 'input::icongroup' === $field['elementType'] ){

				$element = $elementType::IconGroup( $field['id'] ?? '' );

			} elseif( 'input::icheck' === $field['elementType'] ){

				$element = $elementType::iCheck( $field['id'] ?? '' );

			} elseif( 'input::iradio' === $field['elementType'] ){

				$element = $elementType::iRadio( $field['id'] ?? '' );

			} else {

				$element = $elementType::Create( $field['id'] ?? '' );

			}

			if( array_key_exists( 'fields', $field ) ){

				$element->append( $this->buildForm( $field['fields'] ) );

			}

			$this->callElementMethods( $element, $field );

			$html .= $element->buffer();

		}

		return $html;

	}

	protected function callElementMethods( ElementInterface &$element, array $settings ){

		$refObj = new ReflectionObject( $element );

		foreach( $settings as $method => $param ){

			if( $refObj->hasMethod( $method ) ){

				$element->$method( $param );

			}

		}

	}

	public function buffer(){

		return $this->html;

	}

	public function render(){

		echo $this->html;

	}

}
