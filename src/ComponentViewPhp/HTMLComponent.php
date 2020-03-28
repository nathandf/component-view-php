<?php

namespace ComponentViewPhp;

abstract class HTMLComponent implements IComponent
{
	private $valid_element_types = [ "head", "html", "style", "div", "body", "p", "i", "a", "label", "span", "area", "base", "col", "param", "source", "track", "wbr", "link", "script", "aside", "audio", "canvas", "datalist", "data", "code", "sub", "sup", "time", "var", "details", "embed", "output", "progress", "video", "form", "input", "select", "option", "textarea", "meta", "img", "br", "hr", "main", "nav", "header", "footer", "article", "section", "button", "ol", "ul", "li", "iframe", "object", "h1", "h2", "h3", "h4", "h5", "h6", "pre", "blockquote", "cite" ];
	private $empty_elements = [ "area", "base", "col", "embed", "param", "source", "track", "wbr", "meta", "img", "br", "hr", "input", "link" ];
    protected $state = [];
    protected $required_state = [];
    protected $errors = [];

	public function __call( $name, array $args )
	{
        // Returns the first argument input into the raw funtion
        if ( $name == "raw" ) {
           return $args[ 0 ];
        }

		if ( !in_array( $name, $this->valid_element_types ) ) {
			throw new \Exception( "Invalid Element Type: '{$name}' is not a valid html tag type" );
		}

		if ( empty( $args ) ) {
			$args[ 0 ] = "";
		}

		return $this->buildHTMLElement( $name, $args[ 0 ] );
	}

    abstract public function render();

    public function renderFile( $filename )
    {
        $filename = "App/Views/Files/" . $filename;
        
        if ( !file_exists( $filename ) ) {
            return $this->renderComponent( "Errors/ComponentError", [ "message" => "Render Error: File cannot be rendered" ] );
        }

        // Extract the state of the component to make the variables available to the file
        extract( $this->state );

        include( $filename );

        return;
    }

	public function renderComponent( $class_name, array $data = [] )
    {
        try {
            // Build full class name
            $full_class_name = "\\Views\\Components\\" . str_replace( "/", "\\", $class_name );

            // Check if component file exists
            $this->componentExists( $full_class_name );

            $component = new $full_class_name;


            $component->arrayToState( $data );
            
            return $component->render();

        } catch ( \Exception $e ) {
            return $this->renderComponent( "Errors\ComponentError", [ "message" => $e->getMessage() ] );
        }   
    }

    public function componentExists( $class_name )
    {
        if ( !class_exists( $class_name ) ) {
            throw new \Exception( "Component '$class_name' does not exist" );
        }
    }

    public function setState( $key, $value )
    {
        $this->state[ $key ] = $value;
        return $this;
    }

    public function getState( $key = null )
    {
        if ( array_key_exists( $key, $this->state ) ) {
            return $this->state[ $key ];
        }

        return null;
    }

    public function arrayToState( array $array )
    {
        foreach ( $array as $var => $value ) {
            $this->setState( $var, $value );
        }

        return;
    }

    public function objectToState( $object )
    {
        if ( !is_object( $object ) ) {
            throw new \Exception( "Invalid Argument: \$object must be of type 'object'" );
        }

        $props = get_object_vars( $object );

        foreach ( $props as $prop => $value ) {
            $this->setState( $prop, $value );
        }

        return;
    }

    protected function buildHTMLElement( $name, $content )
    {
        $element = new HTMLElement( $name, $content );

        $element->setIsEmptyElement(
            ( in_array( $name, $this->empty_elements ) ? true : false )
        );

        return $element;
    }

    protected function hasRequiredState()
    {
        if ( !empty( $this->required_state ) ) {
            foreach ( $this->required_state as $value ) {
                if ( is_null( $this->getState( $value ) ) ) {
                    throw new \Exception( "Required component state '{$value}' not set in Component '" . get_class( $this ) . "'." );
                }
            }
        }

        return;
    }

    protected function setRequiredState( $value )
    {
        $this->required_state[] = $value;
        return $this;
    }

    protected function addError( $error )
    {
        $this->errors[] = $error;
        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}