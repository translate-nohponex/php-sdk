<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Demo</title>
  </head>
<body>
    <pre>
    <?php
    //Display all errors
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    //Include Translate SDK class
    require 'src/translate.php';

    //Initialize API 
    $translate = new Translate\Translate( '59f1cbb3bee39038d74e9d043daf016a' );

    try{
        //Print all thranslations for 'gr' language
        print_r ( $translate->fetch( '1', 'gr' ) );

    }catch( Translate\TranslateAPIException $e ){
        print( $e->getMessage( ) );
        echo '</pre>';
        die();
    }
    ?>
    </pre>
</body>
</html>