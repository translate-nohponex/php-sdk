<?php
namespace Translate;

/**
 * TranslateAPIException
 */
class TranslateAPIException extends \Exception{
    public function __construct( $error, $code = 400 ){
        parent::__construct( $error, $code );
    }
}
/**
 * Translate class
 * Create a new instance of this class by providing your authentication credentials
 * And execute the available API methods
 */
class Translate {
    const VERSION = '0.0.1';
    const VERSION_INTEGER = 001;
    
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PUT    = 'PUT'; 
    
    const REQUEST_EMPTY_FLAG        = 0;
    const REQUEST_BINARY            = 1; 
    const REQUEST_NOT_URL_ENCODED   = 2;
    
    /**
     * Base url of Translate API
     */
    private $API_URL = 'https://translate.nohponex.gr/';

    private $authentication_credentials = FALSE;
    private $authentication_header = FALSE;
    
    /**
     * Project's API Key
     */
    private $api_key;

    /**
     * Create a new instance of the class using user's email and password as authentication credentials
     * @return Returns an instance of Translate
     */
    public function __construct( $api_key, $useSSL = FALSE ) {
        //Set API key
        $this->api_key = $api_key;

        //ToDo Set API key as Header
        //$this->authentication_header = 'Authorization: Basic ' . base64_encode( $email . ':' . $password );
    }
    
    
    /**
     * Perform an cURL request to API server,
     * this is an internal function
     * @param $resource String Resource fraction of the url for example map/?name=xx 
     * @return Returns an array with the response code and the response, if the accept parameter is set to json the the response will be decoded as json    
     */
    private function request( $resource, $method = Translate::METHOD_GET, $data = NULL, $flags = Translate::REQUEST_EMPTY_FLAG,  $accept = 'application/json', $encoding = NULL ) {
        
        //Create url
        $url = $this->API_URL . $resource . '&api_key=' . $this->api_key;
        
        //Extract flags

        //Is the request binary
        $binary         =    ( $flags & Translate::REQUEST_BINARY          ) != 0;
        //If the request paramters form encoded
        $form_encoded   =  !(( $flags & Translate::REQUEST_NOT_URL_ENCODED ) != 0);
        
        //Initialize headers
        $headers = array(
            'Accept: ' . $accept,
            /*$this->authentication_header*/
        );
        
        //If request's data is encoded provide the Contenty type Header
        if( $form_encoded ){
           $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
        }
        
        //If request has a special Content-Encoding
        if( $encoding ){
            $headers[] = 'Content-Encoding: ' . $encoding;
        }
    
        //Initialize curl
        $handle = curl_init();
        curl_setopt( $handle, CURLOPT_URL, $url );
        curl_setopt( $handle, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $handle, CURLOPT_RETURNTRANSFER, TRUE );
        //Set timeout values ( in seconds )
        curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 1 );
        curl_setopt( $handle, CURLOPT_TIMEOUT, 3 );
        curl_setopt( $handle, CURLOPT_NOSIGNAL, 1 );

        //Security options
        //curl_setopt( $handle, CURLOPT_SSL_VERIFYHOST, FALSE );
        curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, FALSE );
        
        //On binary transfers
        if( $binary ){
            curl_setopt($handle, CURLOPT_BINARYTRANSFER, TRUE );
        }

        //Switch on HTTP Request method
        switch( $method ) {
            case Translate::METHOD_GET :
                break;
            case Translate::METHOD_POST :
                curl_setopt($handle, CURLOPT_POST, true);
                
                if( $data && $form_encoded ){ //Encode fields if required ( URL ENCODED )
                    curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query( $data ) );
                }else if( $data ){
                    curl_setopt($handle, CURLOPT_POSTFIELDS, $data );
                }
                break;
            case Translate::METHOD_PUT :
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, Translate::METHOD_PUT );
                if( $data ){
                    curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode( $data ) );
                }
                break;
            case Translate::METHOD_DELETE :
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, Translate::METHOD_DELETE );
                break;
            default:
                throw new TranslateAPIException( 'Unsupporter method' );
        }

        //Get response
        $response = curl_exec( $handle );
        //Get response code
        $code = curl_getinfo( $handle, CURLINFO_HTTP_CODE );
        
        //Catch curl error
        if( !$response ){
            throw new TranslateAPIException( 'Error: "' . curl_error( $handle ) );
        }
        
        //Throw exception on responce failure
        if( !in_array( $code, array( 200, 201, 202 ) ) ){ // OK, Created, Accepted
            $decoded = json_decode( $response, true );     
            
            throw new TranslateAPIException( $decoded[ 'error' ], $code );
        }
        
        curl_close( $handle );
        
        //Return the data of response
        return ( $accept == 'application/json' ? json_decode( $response, true ) : $response );               
    }

    /**
     * SDK Public Methods
     */


    /**
     * Get all translation keys
     * @throws TranslateAPIException on failure
     * @param $project_id Integer Project's id
     * @param $language String Lanuage Code
     * @return Returns translation object for selected $language
     */
    public function fetch( $project_id, $language ) {

        $p = array( 'id' => $project_id, 'language' => $language );

        $r = $this->request( ( 'fetch/listing?' . http_build_query( $p ) ), Translate::METHOD_GET );
              
        return $r[ 'translation' ];
    }

    /**
     * Add keyword to a project
     */
    public function add_key( $project_id, $keyword ){

        $p = array( 'id' => $project_id, 'key' => $keyword );

        try{
            $r = $this->request( 'fetch/create?', Translate::METHOD_POST, $p );
        }catch( TranslateAPIException $e ){
            //Key exists
            return FALSE;
        }
        return TRUE;
    } 
}
