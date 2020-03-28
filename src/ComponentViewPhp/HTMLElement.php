<?php

namespace ComponentViewPhp;

class HTMLElement
{
	protected $attributes = [];
	protected $classes = [];
	public $type;
	private $inner_content = "";
	private $is_empty_element = false;

	public function __construct( $type, $content = "" )
	{
		$this->type = $type;

		$this->inner_content .= $content;
	}

	public function attr( $key, $value = null )
	{
		$this->attributes[ $key ] = $value;

		return $this;
	}
	
	public function addClass( $class )
	{
		$this->classes[] = $class;

		return $this;
	}

	public function addClasses( array $classes )
	{
		foreach ( $classes as $class ) {
			$this->classes[] = $class;
		}

		return $this;
	}

	private function attributesToHTML()
	{
		if ( !empty( $this->classes ) ) {
			$class_attribute_value = trim( implode( " ", $this->classes ) );
			$this->attr( "class", $class_attribute_value );
		}

		$html = "";
		foreach ( $this->attributes as $key => $value ) {
			if ( is_null( $value ) ) {
				$html .= " {$key}";
				continue;
			}

			$html .= " {$key}=\"{$value}\"";
		}

		return $html;
	}

	public function setIsEmptyElement( $value )
	{
		if ( !is_bool( $value ) ) {
			throw new \Exception( "is_empty_element value must be bool" );
		}

		$this->is_empty_element = $value;

		return $this;
	}

	public function getHTML()
	{
		$html = "<{$this->type}{$this->attributesToHTML()}";
		
		$close = ">{$this->inner_content}</{$this->type}>";
		if ( $this->is_empty_element ) {
			$close = " />";
		}

		return $html .= $close;
	}
}