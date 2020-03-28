<?php

abstract class View
{
    protected $data = [];

    public function renderTemplate( $class_name, array $data = [] )
    {
        $class_name = "Templates\\" . $class_name;

        $this->renderComponent( $class_name, $data );
    }

    public function renderComponent( $class_name, array $data = [] )
    {
        try {
            // Start buffer
            ob_start();

            $full_class_name = "\\Views\\Components\\" . str_replace( "/", "\\", $class_name );

            // Check if component file exists. If true, create component
            $this->componentExists( $full_class_name );
            $component = new $full_class_name;

            // Turn view data into variables into component state
            $component->arrayToState( $data );
            
            echo( $component->render() );

            // Flush buffer
            ob_flush();

        } catch ( \Exception $e ) {
            $this->renderException( $e );
        }
    }

    public function renderFile( $filename )
    {
        $filename = "App/Views/Files/" . $filename;

        if ( !file_exists( $filename ) ) {
            return $this->renderComponent( "Errors/ComponentError", [ "error_message" => "Render Error: File cannot be rendered" ] );
        }

        include( $filename );

        return;
    }

    public function assign( $index, $data, $sanitize = true )
    {
        $this->data[ $index ] = $data;

        return $this;
    }

    public function getData( $index = null )
    {
        if ( !is_null( $index ) ) {
            if ( isset( $this->data[ $index ] ) ) {
                return $this->data[ $index ];
            }

            return;
        }

        return $this->data;
    }

    public function renderException( \Exception $e )
    {
        echo( "<div class='error-message --c-hide'>{$e->getMessage()}</div>" );
    }

    public function redirectWithData( $url, array $data )
    {
        if ( !empty( $data ) ) {
            $url = $url . "?" . http_build_query( $data );
        }

        header( "Location: " . $url );
        exit();
    }

    public function redirect( $redirect_url, $http_response_code = 200 )
    {
        http_response_code( $http_response_code );
        header( "Location: " . $redirect_url );
        exit();
    }

    public function addErrorMessage( $index, $message )
    {
        $this->data[ "error_messages" ][ $index ] = $message;
    }

    public function getVar( $var_name )
    {
        return ( isset( $this->data[ $var_name ] ) ? $this->data[ $var_name ] : null );
    }

    protected function componentExists( $class_name )
    {
        if ( !class_exists( $class_name ) ) {
            throw new \Exception( "Component '$class_name' does not exist" );
        }
    }

    public function stripQueryString( $url )
    {
        if ( strpos( $url, "?" ) ) {
            $url = explode( "?", $url )[ 0 ];
        }

        return $url;
    }
}
