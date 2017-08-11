<?php
require_once( 'auth_chanw.php' );

use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

$github_path        = "/Users/admin/github/velocity/library";
$core_site_name     = "_brisk";
$format_folder_path = "core/library/velocity/chanw";
$git_comment        = "Updated";

try
{
    // 1. update library files
    if( is_dir( $github_path ) && $handle = opendir( $github_path ) )
    {
        while ( false !== ( $file = readdir( $handle ) ) ) 
        {
            if( $file == '.' || $file == '..' || $file == '.DS_Store' )
            {
                continue;
            }
            else
            {
                $file_path = $github_path . '/' . $file;
                
                if( is_file( $file_path ) )
                {
                    if( u\StringUtility::startsWith( $file, "chanw" ) &&
                        u\StringUtility::endsWith( $file, ".vm" ) )
                    {
                        
                        $format_name = substr( $file, 0, strlen( $file ) - 3 );
                        $format = $cascade->getAsset(
                            a\ScriptFormat::TYPE, 
                            $format_folder_path . '/' . $format_name,
                            $core_site_name );
                        $script = $format->getScript();
                        
                        if( $fhw = fopen( $file_path, 'w' ) )
                        {
                            fwrite( $fhw, $script );
                            fclose( $fhw );
                        }
                    }
                }
            }
        }
    }

    // 2. push to github
    shell_exec( 
        "cd /Users/admin/github/velocity;git add -A;" .
        "git commit -m '$git_comment';git push" );
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