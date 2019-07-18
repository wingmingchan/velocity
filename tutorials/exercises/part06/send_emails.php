<?php
require_once( 'auth_REST_SOAP.php' );

use cascade_ws_AOHS      as aohs;
use cascade_ws_constants as c;
use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_utility   as u;
use cascade_ws_exception as e;

$script = "#set( \$globalStringBuffer = \$_.Class.forName( \"java.lang.StringBuffer\" ).getConstructor().newInstance() )\$globalStringBuffer.setLength( 0 )##
#set( \$chanwGetDatabasStatement = \"\" )
#set( \$chanwConnection = \$_.Class.forName( \"javax.naming.InitialContext\" ).getConstructor().newInstance().lookup( \"java:comp/env/jdbc/CascadeDS\" ).Connection )\$chanwConnection.setReadOnly( true )##
#set( \$chanwGetDatabaseStatement = \$chanwConnection.createStatement( \$_FieldTool.in( \"java.sql.ResultSet\" ).TYPE_SCROLL_INSENSITIVE, \$_FieldTool.in( \"java.sql.ResultSet\" ).CONCUR_READ_ONLY ) )
#set( \$chanwGetResultSet = \"\" )
#set( \$chanwGetResultSet = \$chanwGetDatabaseStatement.executeQuery( \"SELECT userName, email, fullName FROM cxml_user WHERE isEnabled=1 AND email is not null \" ) )
#if( \$chanwGetResultSet.last() )#set( \$chanwLastRecordNumber = \$chanwGetResultSet.Row )#end
#set( \$chanwColumnCount = \$chanwGetResultSet.getMetaData().getColumnCount() )##
#set( \$void = \$globalStringBuffer.append( '\$view=array(' ) )
#if( \$chanwLastRecordNumber > 0 )
#set( \$void = \$chanwGetResultSet.first() )##
#foreach( \$count in [ 1..\$chanwColumnCount ] )##
#if( \$count == 1 )#set( \$void = \$globalStringBuffer.append( '\"' ).append( \$chanwGetResultSet.getString( \$count ) ).append( '\"=>array(' ) )##
#else#set( \$void = \$globalStringBuffer.append( '\"' ).append( \$chanwGetResultSet.getString( \$count ) ).append( '\"' ) )##
#if( \$count == \$chanwColumnCount )#set( \$void = \$globalStringBuffer.append( \"),\" ) )##
#else#set( \$void = \$globalStringBuffer.append( \",\" ) )#end#end#end##
#foreach( \$i in [ 2..\$chanwLastRecordNumber ] )#set( \$void = \$chanwGetResultSet.next() )##
#foreach( \$j in [ 1..\$chanwColumnCount ] )##
#if( \$j == 1 )#set( \$void = \$globalStringBuffer.append( '\"' ).append( \$chanwGetResultSet.getString( \$j ) ).append( '\"=>array(' ) )##
#else#set( \$void = \$globalStringBuffer.append( '\"' ).append( \$chanwGetResultSet.getString( \$j ) ).append( '\"' ) )##
#if( \$j == \$chanwColumnCount )#set( \$void = \$globalStringBuffer.append( \"),\" ) )##
#else#set( \$void = \$globalStringBuffer.append( \",\" ) )#end#end#end#end#end##
#set( \$void = \$globalStringBuffer.append( \");\" ) )
<php>\$globalStringBuffer.toString()</php>";
$format_name = "db-users";
$folder_path = "_cascade/formats/intermediate/09_ws_and_db";
$site_name   = "formats";

try
{
	// create the velocity format
	$format = $admin->getScriptFormat( 
		"$folder_path/$format_name", $site_name );
	if( is_null( $format ) )
	{
		$format = $admin->createScriptFormat(
			$admin->getAsset( a\Folder::TYPE, $folder_path, $site_name ),
			$format_name, $script );
	}
	sleep( 2 );
	// create and publish the page
	$page = $admin->getPage( $format_name, $site_name );
	
	if( is_null( $page ) )
	{
		$page = $admin->createXhtmlPage(
			$admin->getAsset( a\Folder::TYPE, "/", $site_name ),
			$format_name, "",
			$admin->getAsset( a\ContentType::TYPE, "XML", "_brisk" ) );
	}
	sleep( 2 );
	$page->setRegionFormat( "XML", "DEFAULT", $format )->edit()->publish();
	sleep( 10 );
	// get PHP code
	$code = file_get_contents( "http://www.upstate.edu/$site_name/$format_name.xml" );
	$code = str_replace( "&gt;", ">", str_replace( "<php>", "", str_replace( "</php>", "", $code ) ) );
	eval( $code );
	// send emails here
	foreach( $view as $user_id => $user_info )
	{
		echo $user_id, ",", $user_info[ 0 ], ",", $user_info[ 1 ], BR;
	}
	// clean up
	$page->unpublish();
	sleep( 5 );
	$admin->deleteAsset( $page );
	$admin->deleteAsset( $format );
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