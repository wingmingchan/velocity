<?php
// last run: 05/29/2018
$start_time = time();
$site_name           = "_wing";                      // name of the site to be upgraded
$library_folder_path = "core/library/velocity/chanw"; // path of the folder to be upgraded
$source = "http://www.upstate.edu/standard-model/source/";

/*
This program reads source code from the Upstate server,
and updates all library formats by wing.
*/

require_once('auth_REST_SOAP.php');  // to sandbox

use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

try
{
    u\DebugUtility::setTimeSpaceLimits();
    
    // trim leading and trailing slashes and space
    $library_folder_path = trim( $library_folder_path, '/ ' );

    // retrieve library folder
    $velocity_folder = $cascade->getAsset(
        a\Folder::TYPE, $library_folder_path, $site_name
    );
    
    $library_format_names = array(
        "chanw-database-utilities",
        "chanw-date-time-utilities",
        "chanw-display-velocity-code",
        "chanw-doc-xml-utilities",
        "chanw-element-utilities",
        "chanw-global-deque",
        "chanw-global-queue",
        "chanw-global-stack",
        "chanw-html-builder",
        "chanw-html-builder-with-deque",
        "chanw-initialization",
        "chanw-library-import",
        "chanw-process-cascade-api",
        "chanw-process-index-block",
        "chanw-process-xml",
        "chanw-reflect-utilities",
        "chanw-regex-utilities",
        "chanw-rss-utilities",
        "chanw-service-provider",
        "chanw-tree-utilities",
        "chanw-ws-utilities",
        "chanw-xslt-utilities",
        "upstate-database"
    );
    
    // create formats needed and push the contents
    foreach( $library_format_names as $library_format_name )
    {
        try
        {
            $format = $cascade->getAsset( 
                a\ScriptFormat::TYPE,
                $velocity_folder->getPath() . "/" . $library_format_name,
                $site_name );
        }
        catch( e\NullAssetException $e )
        {
            $format = $cascade->createScriptFormat(
                $velocity_folder,
                $library_format_name,
                "##"
            );
        }
        $source_path = $source . $library_format_name . ".xml";
        pushSource( $site_name, $library_folder_path, $format, $source_path );
    }
    
    u\DebugUtility::outputDuration( $start_time );
}
catch( \Exception $e )
{
    echo S_PRE . $e . E_PRE;
    u\DebugUtility::outputDuration( $start_time );
}
catch( \Error $er )
{
    echo S_PRE . $er . E_PRE;
    u\DebugUtility::outputDuration( $start_time );
}

function pushSource( $site_name, $library_folder_path, $asset, $source_path )
{
    $source_content = file_get_contents( $source_path );

    if( $source_content != "" )
    {
        $source_content = str_replace( "<code>", "", $source_content );
        $source_content = str_replace( "</code>", "", $source_content );
        $source_content = str_replace( "&lt;", "<", $source_content );
        $source_content = str_replace( "&gt;", ">", $source_content );
        $source_content = str_replace( "&amp;", "&", $source_content );
        $source_content = str_replace( 
            "_brisk", $site_name, $source_content );
        $source_content = str_replace( 
            "core/library/velocity/chanw", $library_folder_path, $source_content );
        $asset->setScript( $source_content )->edit();
    }
}
?>