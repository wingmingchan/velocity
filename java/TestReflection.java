import java.util.Calendar;
import java.util.GregorianCalendar;
import java.lang.reflect.*;

/*
javac -Xlint TestReflection.java
*/

public class TestReflection
{
    public static void main( String[] args )
    {
        try
        {
            /* test Class class */
            Class calClass  = Class.forName( "java.util.Calendar" );
            Class gCalClass = Class.forName( "java.util.GregorianCalendar" );
            
            /* retrieve constructors */
            Constructor[] calConstructors  = calClass.getConstructors();
            Constructor[] gCalConstructors = gCalClass.getConstructors();
                
            System.out.println(
                "Class name: " + calClass.getName() + "\n" +
                "Number of constructors: " + calConstructors.length + "\n" +
                "Class name: " + gCalClass.getName() + "\n" +
                "Number of constructors: " + gCalConstructors.length // 7
            );
            
            /* test constructor */
            for( Constructor c : gCalConstructors )
            {
                Class[] paramTypes = c.getParameterTypes();
                
                // find the default constructor
                if( paramTypes.length == 0 )
                {
                    System.out.println( "Default constructor found." );
                    
                    // construct an object
                    GregorianCalendar gCal = ( GregorianCalendar ) c.newInstance();
                    // output some information
                    System.out.println( gCal.get( Calendar.YEAR ) ); // 2017
                }
                
                // find the constructor taking 3 params
                if( paramTypes.length == 3 )
                {
                    GregorianCalendar gCalWithParams = 
                        ( GregorianCalendar ) c.newInstance( 2018, 0, 1 );
                    System.out.println( gCalWithParams.get( Calendar.YEAR ) ); // 2018    
                }
            }
            
            /* test fields */
            Field april = calClass.getField( "APRIL" );
            System.out.println( april.getType().getName() ); // int
            
            /* test method */
            Method calMethod = calClass.getMethod( "getInstance" );
            System.out.println( calMethod.getName() ); // getInstance
            
            GregorianCalendar c = ( GregorianCalendar ) calMethod.invoke( null );
            System.out.println( c.get( Calendar.YEAR ) ); // 2017
        }
        catch( ClassNotFoundException e )
        {
            System.out.println( e.getMessage() );
        }
        catch( Exception e )
        {
            System.out.println( e.getMessage() );
        }
    }
}