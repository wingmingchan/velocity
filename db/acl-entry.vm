#import( 'site://_common_assets/formats/library/velocity/chanw_object_creator' )
#chanwGetObjectByClassName( 'edu.upstate.chanw.db.CascadeDB' )

#set( $db   = $chanwGetObjectByClassName )
#set( $sql  = "select count(*) from cxml_aclentry " )
#set( $data = $db.getResultSet( $sql ) )

#if( $data.next() )
<pre>

$data.getString( 1 )
</pre>
#end

$db.finalize()