<?php
$start_time = time();
$site_name = "_common_assets_barebone"; // name of the site to be upgraded
//$site_name = "_common_assets"; // name of the site to be upgraded
$source    = "http://www.upstate.edu/cascade-admin/standard-model/source/";

/*
This program reads source code from the Upstate server,
and updates all library formats and xml blocks.
*/

require_once('auth_tutorial7.php');

use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

try
{
    u\DebugUtility::setTimeSpaceLimits();

    // folder paths
    $formats_folder = $cascade->getAsset(
        a\Folder::TYPE, "formats", $site_name
    );
    $velocity_folder = $cascade->getAsset(
        a\Folder::TYPE, "formats/library/velocity", $site_name
    );
    
    // delete unused blocks and formats
    $blocks_to_be_deleted = array(
        "chanw_global_values_code",
        "chanw_global_velocity_code",
        "chanw_object_creator_code",
    );
    
    $formats_to_be_deleted = array(
        "chanw_global_utility_objects",
        "chanw_structured_data_worker"
    );
    
    foreach( $blocks_to_be_deleted as $block_to_be_deleted )
    {
        $cascade->deleteXmlBlock(
            $velocity_folder->getPath() . "/" . $block_to_be_deleted,
            $site_name );
    }
    
    foreach( $formats_to_be_deleted as $format_to_be_deleted )
    {
        $cascade->deleteScriptFormat(
            $velocity_folder->getPath() . "/" . $format_to_be_deleted,
            $site_name );
    }
    
    $library_format_names = array(
        "chanw_database_utilities",
        "chanw_display_velocity_code",
        "chanw_global_deque",
        "chanw_global_macros",
        "chanw_global_queue",
        "chanw_global_stack",
        "chanw_global_values",
        "chanw_html_builder",
        "chanw_html_builder_with_deque",
        "chanw_library_import",
        "chanw_local_date_time_utilities",
        "chanw_object_creator",
        "chanw_process_cascade_api",
        "chanw_process_index_block",
        "chanw_process_xml",
        "chanw_reflect_utilities",
        "chanw_service_provider",
        "chanw_sorted_pages",
        "upstate_database"
    );
    
    $library_block_names = array(
        "chanw_global_code_templates"
    );
    
    // create blocks needed and push the contents
    foreach( $library_block_names as $library_block_name )
    {
        try
        {
            $block = $cascade->getAsset( 
                a\XmlBlock::TYPE,
                $velocity_folder->getPath() . "/" . $library_block_name,
                $site_name );
        }
        catch( e\NullAssetException $e )
        {
            $block = $cascade->createXmlBlock(
                $velocity_folder,
                $library_block_name,
                "<code></code>"
            );
        }
        $source_path = $source . $library_block_name . ".xml";
        pushSource( $site_name, $block, $source_path );
    }

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
        pushSource( $site_name, $format, $source_path );
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

function pushSource( $site_name, $asset, $source_path )
{
    $source_content = file_get_contents( $source_path );

    if( $source_content != "" )
    {
        $type = $asset->getType();
        
        if( $type == a\XmlBlock::TYPE )
        {
            $source_content = str_replace( 
                "_common_assets", $site_name, $source_content );
            $asset->setXml( $source_content )->edit();
        }
        else
        {
            $source_content = str_replace( "<code>", "", $source_content );
            $source_content = str_replace( "</code>", "", $source_content );
            $source_content = str_replace( "&lt;", "<", $source_content );
            $source_content = str_replace( "&gt;", ">", $source_content );
            $source_content = str_replace( "&amp;", "&", $source_content );
            $source_content = str_replace( 
                "_common_assets", $site_name, $source_content );
            $asset->setScript( $source_content )->edit();
        }
    }
}
?>