<?php
    //Get requested language
    $language = 'en';
    $languages = array( 'en', 'gr', 'de' );
    if( isset( $_GET[ 'language' ] ) && in_array( $_GET[ 'language' ], $languages ) ){
        $language = $_GET[ 'language' ];
    }
?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
  <head>
    <meta charset="utf-8">
    <title>PHP - Demo</title>
    <link href="css/flag-sprites/flags.css" rel="stylesheet">
    <style>
        body{
            margin-top:50px;
            margin-left:auto;
            margin-right:auto;
            width:70%;
        }
        small,span{
            color: #FF6600;
            text-decoration: underline;
            font-weight: 800;
        }
        pre{
            background-color: #F9F9F9;
            border: 1px dashed #2F6FAB;
            color: black;
            line-height: 1.1em;
            padding: 1em;
        }
    </style>
  </head>
<body>
    <div>
        Language : <span><?php echo $language; ?></span>
        <div>
        <a href="?language=en">Change to EN <span class="flag flag-gr"></span></a>
        <a href="?language=gr">Change to GR <span class="flag flag-en"></span></a>
        <a href="?language=de">Change to DE <span class="flag flag-de"></span></a>
        <hr/>
    </div>
    <div class="container">
        <h2>Initialize API</h2>
        <?php
            //Project specific settings
            $project_id = 1;
            $api_key = '59f1cbb3bee39038d74e9d043daf016a';

            //Display all errors
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            //Include Translate SDK class
            require '../src/translate.php';

            //Initialize API 
            $translate = new Translate\Translate( $api_key );
        ?>

        <h3>Show all translations as PHP Array</h3>
        <pre><?php
                try{
                    //Print all thranslations for selected language
                    print_r ( $translate->fetch( $project_id, $language ) );

                }catch( Translate\TranslateAPIException $e ){
                    print( $e->getMessage( ) );
                    echo '</pre>';
                    die();
                }
            ?>
        </pre>
        <h2>Use language models</h2>
        <?php
        //Use language models 
        
        //Initialize strings as empty array
        $strings = array();

        //Require language models
        require "models/language.php";

        //Require language strings for selected language
        require "language/$language.php";
        ?>
        
        <h3>Return a translated key</h3>
        <pre> 
            <span><?php echo __( 'title' ); ?></span>
        </pre>
        <h3>Print a translated key </h3>
        <pre> 
            <span><?php ___( 'usage' ); ?></span>
        </pre>
        <h3>Use translated template</h3>
        <pre> 
            <span><?php echo language::template( __( 'hello' ), array( 'username' => 'NohponeX', 'email' => 'nohponex@gmail.com' ) ); ?></span>
        </pre>
    </div>    
</body>
</html>