<?php

namespace ComponentViewPhp;

abstract class HTMLTemplate extends HTMLComponent
{
	protected $blocks = [];

	public function extendsTemplate( $class_name )
	{
		$full_class_name = "Views\\Components\\Templates\\" . str_replace( "/", "\\", $class_name );

		if ( !class_exists( $full_class_name ) ) {
            throw new \Exception( "Template '$class_name' does not exist" );
        }

		return new $full_class_name;
	}

	public function block( $name, $content )
	{
		$this->blocks[ $name ] = $content;

		return $this;
	}

	public function renderBlock( $name )
	{
		if ( isset( $this->blocks[ $name ] ) ) {
			return $this->blocks[ $name ];
		}

		return;
	}
}