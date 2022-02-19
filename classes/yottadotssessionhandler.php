<?php
/* Info used to build this script
 * http://www.hackingwithphp.com/10/3/7/files-vs-databases
 * https://medium.com/@lekker/custom-session-handler-using-mysql-7868fccf1621
 * https://stackoverflow.com/questions/36753513/how-do-i-save-php-session-data-to-a-database-instead-of-in-the-file-system
 * https://github.com/dominicklee/PHP-MySQL-Sessions/blob/master/mysql.sessions.php
 *
 */
class yottadotssessionhandler implements SessionHandlerInterface {
    //SessionUpdateTimestampHandlerInterface
    /*
     * Datacolumns in the database:
     * idsessiondata (compatible with session_id
     * sessiondata
     * lastchanged (timestamp with timezone)
     *
     * session_id
     */
    private $dbconnection = '';
    public function __construct() {
        //open databse connection
        $this->dbconnection = pg_connect("host=".DBHOSTSESSION." dbname=".DBNAMESESSION." user=".DBUSERNAMESESSION." password=".DBPASSWORDSESSION."");
        session_set_save_handler (
            array($this, 'open')
            ,array($this, 'close')
            ,array($this, 'read')
            ,array($this, 'write')
            ,array($this, 'destroy')
            ,array($this, 'gc')
            ,array($this, 'create_sid')
        // ,array($this, 'validate_sid')
        // ,array($this, 'update_timestamp')
        );
//        echo 'setup the database connection'.PHP_EOL;
    }
    public function close(){
        // return value should be true for success or false for failure
        // ...
        //close the database connection)
        if(pg_close($this -> dbconnection)){
//            echo 'Session has closed'.PHP_EOL;
            return true;
        } else {
//            echo 'Session close went wrong'.PHP_EOL;
            return false;
        }
    }
    public function destroy($sessionId){
        // return value should be true for success or false for failure
        // ...
        //delete the session ID
        $qDelete = ' DELETE FROM sessiondata WHERE idsessiondata=\''.pg_escape_string($sessionId).'\'';
        if(pg_query($this -> dbconnection, $qDelete)) {
//            echo 'Session has destroyed: '.$sessionId.PHP_EOL;
            return true;
        } else {
//            echo '$qDelete'.$qDelete.PHP_EOL;
            return false;
        }
    }
    public function gc($maximumLifetime){
        // return value should be true for success or false for failure
//	    echo 'gc '.$maximumLifetime.PHP_EOL;
        // ...
        return true;
    }
    public function open($sessionSavePath, $sessionName){
        // return value should be true for success or false for failure

//        echo 'Session has opend'.PHP_EOL;
        $nr=session_id();
//	    echo 'session_id'.strlen($nr).PHP_EOL;
//	    echo 'sessionid'.$nr.PHP_EOL;
//        echo '$sessionSavePath : '.$sessionSavePath.PHP_EOL;
//        echo '$sessionName: '.$sessionName.PHP_EOL;
        return true;
    }
    public function read($sessionId){
        // return value should be the session data or an empty string
        $qSelect = 'SELECT sessiondata FROM sessiondata WHERE idsessiondata=\''.pg_escape_string($sessionId).'\'';
        $qResult = pg_query($this->dbconnection, $qSelect);
        if($qResult) {
            $aResult = pg_fetch_all($qResult);//pg_fetch_all returns an array with values or false when nothing has been found
            if($aResult) {
                if(count($aResult) > 0 ) {
//                    echo  'Session read1'.PHP_EOL;
//                    echo '$sessionId'.$sessionId.PHP_EOL;
//	                print_r($aResult);
                    return $aResult[0]['sessiondata'];
                } else {
//                    echo  'Session read2'.PHP_EOL;
//                    echo  '$aResult empty'.PHP_EOL;
//                    echo '$sessionId'.$sessionId.PHP_EOL;
                    return '';
                }
            } else {
//                echo  'Session read3'.PHP_EOL;
//                echo  '$aResult empty or nothing was found'.PHP_EOL;
//                echo '$sessionId'.$sessionId.PHP_EOL;
                return '';
            }
        } else {
//            echo  'Session read4'.PHP_EOL;
//            echo  '$aResult empty because an error accured'.PHP_EOL;
//            echo '$sessionId'.$sessionId.PHP_EOL;
            return '';
        }
    }
    public function write($sessionId, $sessionData){
        // return value should be true for success or false for failure
//        echo 'Session write'.PHP_EOL;
//        echo '$sessionId : '.$sessionId.PHP_EOL;
//        echo '$sessionData: '.$sessionData.PHP_EOL;
//
        $qUpdate = 'UPDATE sessiondata
            SET sessiondata = \''.pg_escape_string($sessionData).'\'
            , lastchanged = now()
            WHERE idsessiondata=\''.pg_escape_string($sessionId).'\'';
        $qResult = pg_query($this->dbconnection, $qUpdate);

        if($qResult) {
            return true;
        } else {
            return false;
        }
    }

    public function create_sid()
    {
        $qSelect = ' SELECT createuniquesessionid();';
        $qResult = pg_query($this->dbconnection, $qSelect);
        if($qResult) {
            $aResult = pg_fetch_all($qResult);
//            echo 'session id created '.$aResult[0]['createuniquesessionid'];
            return $aResult[0]['createuniquesessionid'];
        } else {
            return '';
        }
    }
}