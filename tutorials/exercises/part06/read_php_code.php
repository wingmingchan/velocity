<?php
require_once( 'auth_REST_SOAP.php' );

use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

try
{
    // to run the program
    // http://localhost:8888/velocity/versions/read_versions.php?id=848bec878b7f0856017e07ae5352db06
    
    // get the id string
    $id   = $_GET[ "id" ];
    
    // if the id is not defined, quit
    if( !isset( $id ) )
    {
        echo "Can't retrieve versions without a valid id";
        return;
    }
    
    // update the format named asset-info
    $admin->getAsset( a\ScriptFormat::TYPE, "a414e2468b7f08ee615c70c35ce70232" )->
        setScript( "#set( \$asset_id = \"$id\" )" )->edit();
        
    // publish the page
    $admin->getAsset( a\Page::TYPE, "a40e60968b7f08ee615c70c339d12947" )->publish();
    
    // wait 10 seconds
    sleep( 10 );
    
    // read the page
    $page_content = file_get_contents(
        "http://www.upstate.edu/formats/velocity/courses/exercises/part06/index.php" );

    // get the published db content
    if( $page_content !== false )
    {
        // locate the PHP code
        $start_tag = "<span id=\"db_content\">";
        $start_pos = strpos( $page_content, $start_tag ) + strlen( $start_tag );
        
        if( $start_pos !== false )
        {
            $end_pos = strpos( $page_content, "##end of db_content" );
        }
        
        $db_content = substr( $page_content, $start_pos, $end_pos - $start_pos );
        $db_content = str_replace( "&gt;", ">", $db_content );
        
        // evaluate the code
        eval( $db_content );
        
        // output the records
        foreach( $records as $id => $comment )
        {
            echo $id . "=>" . $comment . BR;
        }
    }
}
catch( \Exception $e ) 
{
    echo S_PRE . $e . E_PRE; 
}
catch( \Error $er )
{
    echo S_PRE . $er . E_PRE; 
}
?>