<?php
/**
 * Return translated string
 */
function __( $key ) {
    global $strings;
    
    if( $strings && array_key_exists( $key, $strings ) ) {
        return $strings[ $key ];
    }
    //Track not found strings 
    //TODO
    return $key;
}
/**
 * Print tranlated string
 */
function ___( $key ) {
    global $strings;
    
    if( $strings && array_key_exists( $key, $strings ) ) {
        echo $strings[ $key ];
        return;
    }
    //Track not found strings
    //TODO
    echo $key;
}
class language{
    /**
     * Replace %key% from template string with their value from parameters array  
     */
    public static function template( $template, $parameters ) {
            
        foreach( $parameters as $key => $value ) {
            $template = str_replace( '%' . $key . '%', $value, $template );
        }
        return $template;
    }
    
}
?>