<?php
$site_name = "_common_assets_barebone"; // name of the site to be upgraded
$source    = "http://www.upstate.edu/cascade-admin/standard-model/source/";

/*
This program reads source code from the Upstate server,
and update all library formats and xml blocks.
*/

require_once('auth_tutorial7.php');

// to prevent time-out
set_time_limit( 10000 );
// to prevent using up memory when traversing a large site
ini_set( 'memory_limit', '2048M' );

use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

try
{
    $formats_folder = $cascade->getAsset(
        a\Folder::TYPE, "formats", $site_name
    );
    $velocity_folder = $cascade->getAsset(
        a\Folder::TYPE, "formats/library/velocity", $site_name
    );
    
    $library_format_names = array(
        "chanw_database_utilities",
        "chanw_global_deque",
        "chanw_global_queue",
        "chanw_global_stack",
        "chanw_global_utility_objects",
        "chanw_global_values",
        "chanw_global_values",
        "chanw_global_values_code",   // xml block
        "chanw_global_velocity_code", // xml block
        "chanw_html_builder",
        "chanw_html_builder_with_deque",
        "chanw_library_import",
        "chanw_object_creator",
        "chanw_process_index_block",
        "chanw_process_xml",
        "chanw_reflect_utilities",
        "chanw_sorted_pages",
        "chanw_structured_data_worker",
        "upstate_database"
    );
    
    foreach( $library_format_names as $library_format_name )
    {
        if( u\StringUtility::endsWith( $library_format_name, "_code" ) )
        {
            $asset = $cascade->getAsset( 
                a\XmlBlock::TYPE,
                $velocity_folder->getPath() . "/" . $library_format_name,
                $site_name );
            $block = true;
        }
        else
        {
            $asset = $cascade->getAsset( 
                a\ScriptFormat::TYPE,
                $velocity_folder->getPath() . "/" . $library_format_name,
                $site_name );
            $block = false;
        }

        $source_path    = $source . $library_format_name . ".xml";
        $source_content = file_get_contents( $source_path );

        if( $source_content != "" )
        {
            if( $block )
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