<?php

/**
 * Description of controller
 * @author Kevin
 */
class module_controller
{

    static $flash_messanger = array();

    #########################################################
    # Htpasswd DAO (Data Access Object)                     #
    #########################################################

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_zvps_htpasswd_file_id
     * @return array
     */
    static function fetchFile( $x_zvps_htpasswd_zpanel_user_id, $x_zvps_htpasswd_file_id )
    {
        global $zdbh;
        $sqlString = "SELECT * FROM x_zvps_htpasswd_file WHERE x_zvps_htpasswd_zpanel_user_id = :x_zvps_htpasswd_zpanel_user_id AND x_zvps_htpasswd_file_id = :x_zvps_htpasswd_file_id";
        $bindArray = array( ':x_zvps_htpasswd_file_id' => $x_zvps_htpasswd_file_id, ':x_zvps_htpasswd_zpanel_user_id' => $x_zvps_htpasswd_zpanel_user_id );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->returnRow();
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_zvps_htpasswd_zpanel_user_id
     * @return array
     */
    static function fetchFileList( $x_zvps_htpasswd_zpanel_user_id )
    {
        global $zdbh;
        $sqlString = "SELECT * FROM x_zvps_htpasswd_file WHERE x_zvps_htpasswd_zpanel_user_id = :x_zvps_htpasswd_zpanel_user_id";
        $bindArray = array( ':x_zvps_htpasswd_zpanel_user_id' => $x_zvps_htpasswd_zpanel_user_id );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->returnRows();
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_zvps_htpasswd_user_id
     * @return array
     */
    static function fetchUser( $x_zvps_htpasswd_user_id )
    {
        global $zdbh;
        $sqlString = "SELECT * FROM x_zvps_htpasswd_user WHERE x_zvps_htpasswd_user_id = :x_zvps_htpasswd_user_id";
        $bindArray = array( 'x_zvps_htpasswd_user_id' => $x_zvps_htpasswd_user_id );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->returnRow();
    }

    /**
     * 
     * @global db_driver $zdbh
     * @return array
     */
    static function fetchUserList()
    {
        global $zdbh;
        $sqlString = "SELECT * FROM x_zvps_htpasswd_user";
        $zdbh->bindQuery( $sqlString, array( ) );
        return $zdbh->returnRows();
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param type $x_zvps_htpasswd_file_id
     */
    static function fetchFileUserList( $x_zvps_htpasswd_file_id, $x_zvps_htpasswd_zpanel_user_id )
    {
        global $zdbh;
        $sqlString = "
            SELECT * FROM x_zvps_htpasswd_file f
            INNER JOIN x_zvps_htpasswd_mapper m ON f.x_zvps_htpasswd_file_id=m.x_zvps_htpasswd_file_id
            INNER JOIN x_zvps_htpasswd_user u ON m.x_zvps_htpasswd_user_id=u.x_zvps_htpasswd_user_id
            WHERE f.x_zvps_htpasswd_file_id = :x_zvps_htpasswd_file_id
            AND f.x_zvps_htpasswd_zpanel_user_id = :x_zvps_htpasswd_zpanel_user_id
        ";
        $bindArray = array(
            ':x_zvps_htpasswd_file_id' => $x_zvps_htpasswd_file_id,
            ':x_zvps_htpasswd_zpanel_user_id' => $x_zvps_htpasswd_zpanel_user_id,
        );
        $zdbh->bindQuery($sqlString, $bindArray);
        return $zdbh->returnRows();
    }
    
    static function fetchUserDomainList(){}
    static function fetchUserSubDomainList(){}

    #########################################################

    /**
     * @global db_driver $zdbh
     * @param array $fileArray
     * @return int
     */
    static function createFile( array $fileArray )
    {
        global $zdbh;
        $sqlString = "
            INSERT INTO x_zvps_htpasswd_file 
            ( 
                x_zvps_htpasswd_file_target, 
                x_zvps_htpasswd_file_message, 
                x_zvps_htpasswd_file_created, 
                x_zvps_htpasswd_zpanel_user_id
            )
            VALUES
            (
                :x_zvps_htpasswd_file_target, 
                :x_zvps_htpasswd_file_message, 
                :x_zvps_htpasswd_file_created, 
                :x_zvps_htpasswd_zpanel_user_id
            )
        ";
        $bindArray = array(
            ':x_zvps_htpasswd_file_target'    => $fileArray[ 'x_zvps_htpasswd_file_target' ],
            ':x_zvps_htpasswd_file_message'   => $fileArray[ 'x_zvps_htpasswd_file_message' ],
            ':x_zvps_htpasswd_file_created'   => $fileArray[ 'x_zvps_htpasswd_file_created' ],
            ':x_zvps_htpasswd_zpanel_user_id' => $fileArray[ 'x_zvps_htpasswd_zpanel_user_id' ],
        );
        try {
            $zdbh->bindQuery( $sqlString, $bindArray );
        }
        catch (PDOException $exc) {
            $message = ($exc->getCode() === '23000') ? 'Folder already protected.' : 'Error adding to database.';
            self::setFlashMessage('error', $message);
        }

        
        return $zdbh->lastInsertId();
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param array $userArray
     * @return int
     */
    static function createUser( array $userArray )
    {
        global $zdbh;
        $sqlString = "
            INSERT INTO x_zvps_htpasswd_user
            (
                x_zvps_htpasswd_user_username,
                x_zvps_htpasswd_user_password,
                x_zvps_htpasswd_user_created
            )
            VALUES
            (
                :x_zvps_htpasswd_user_username,
                :x_zvps_htpasswd_user_password,
                :x_zvps_htpasswd_user_created
            )
        ";
        $bindArray = array(
            ':x_zvps_htpasswd_user_username' => $userArray[ 'x_zvps_htpasswd_user_username' ],
            ':x_zvps_htpasswd_user_password' => $userArray[ 'x_zvps_htpasswd_user_password' ],
            ':x_zvps_htpasswd_user_created'  => $userArray[ 'x_zvps_htpasswd_user_created' ]
        );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->lastInsertId();
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_zvps_htpasswd_file_id
     * @param int $x_zvps_htpasswd_user_id
     * @return int
     */
    static function createMapper( $x_zvps_htpasswd_file_id, $x_zvps_htpasswd_user_id )
    {
        global $zdbh;
        $x_zvps_htpasswd_file_id = (int) $x_zvps_htpasswd_file_id;
        $x_zvps_htpasswd_user_id = (int) $x_zvps_htpasswd_user_id;
        $sqlString               = "
            INSERT INTO x_zvps_htpasswd_mapper
            (
                x_zvps_htpasswd_file_id,
                x_zvps_htpasswd_user_id
            )
            VALUES
            (
                :x_zvps_htpasswd_file_id,
                :x_zvps_htpasswd_user_id
            )
        ";
        $bindArray = array(
            ':x_zvps_htpasswd_file_id' => $x_zvps_htpasswd_file_id,
            ':x_zvps_htpasswd_user_id' => $x_zvps_htpasswd_user_id,
        );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->lastInsertId();
    }

    #########################################################

    /**
     * 
     * @global db_driver $zdbh
     * @param array $fileArray
     * @return int
     */
    static function updateFile( $fileArray )
    {
        global $zdbh;
        $sqlString = "
            UPDATE x_zvps_htpasswd_file SET
            x_zvps_htpasswd_file_target = :x_zvps_htpasswd_file_target,
            x_zvps_htpasswd_file_message = :x_zvps_htpasswd_file_message
            WHERE x_zvps_htpasswd_file_id = :x_zvps_htpasswd_file_id
        ";
        $bindArray = array(
            ':x_zvps_htpasswd_file_id'      => $fileArray[ 'x_zvps_htpasswd_file_id' ],
            ':x_zvps_htpasswd_file_target'  => $fileArray[ 'x_zvps_htpasswd_file_target' ],
            ':x_zvps_htpasswd_file_message' => $fileArray[ 'x_zvps_htpasswd_file_message' ],
        );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->returnResult();
    }

    static function updateUser( $userArray )
    {
        global $zdbh;
        $sqlString = "
            UPDATE x_zvps_htpasswd_user SET
            x_zvps_htpasswd_user_username = :x_zvps_htpasswd_user_username,
            x_zvps_htpasswd_user_password = :x_zvps_htpasswd_user_password
            WHERE
            x_zvps_htpasswd_user_id = :x_zvps_htpasswd_user_id
        ";
        $bindArray = array(
            ':x_zvps_htpasswd_user_id'       => $userArray[ 'x_zvps_htpasswd_user_id' ],
            ':x_zvps_htpasswd_user_username' => $userArray[ 'x_zvps_htpasswd_user_username' ],
            ':x_zvps_htpasswd_user_password' => $userArray[ 'x_zvps_htpasswd_user_password' ],
        );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->returnResult();
    }

    #########################################################

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_zvps_htpasswd_file_id
     * @return int
     */
    static function deleteFile( $x_zvps_htpasswd_file_id )
    {
        global $zdbh;
        $sqlString = "
            UPDATE x_zvps_htpasswd_file SET
            x_zvps_htpasswd_file_deleted = UNIX_TIMESTAMP()
            WHERE x_zvps_htpasswd_file_id = :x_zvps_htpasswd_file_id
        ";
        $bindArray = array( ':x_zvps_htpasswd_file_id' => $x_zvps_htpasswd_file_id );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->returnResult();
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_zvps_htpasswd_user_id
     * @return int
     */
    static function deleteUser( $x_zvps_htpasswd_user_id )
    {
        global $zdbh;
        $sqlString = "
            UPDATE x_zvps_htpasswd_user SET
            x_zvps_htpasswd_user_deleted = :x_zvps_htpasswd_user_deleted
            WHERE x_zvps_htpasswd_user_id = :x_zvps_htpasswd_user_id
        ";
        $bindArray = array( ':x_zvps_htpasswd_user_id' => $x_zvps_htpasswd_user_id );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->returnResult();
    }

    /**
     * 
     * @global db_driver $zdbh
     * @param int $x_zvps_htpasswd_file_id
     * @param int $x_zvps_htpasswd_user_id
     * @return int
     */
    static function deleteMapper( $x_zvps_htpasswd_file_id, $x_zvps_htpasswd_user_id )
    {
        global $zdbh;
        $sqlString = "
            DELETE FROM x_zvps_htpasswd_mapper WHERE
            x_zvps_htpasswd_file_id = :x_zvps_htpasswd_file_id
            AND
            x_zvps_htpasswd_user_id = :x_zvps_htpasswd_user_id
        ";
        $bindArray = array(
            ':x_zvps_htpasswd_file_id' => $x_zvps_htpasswd_file_id,
            ':x_zvps_htpasswd_user_id' => $x_zvps_htpasswd_user_id
        );
        $zdbh->bindQuery( $sqlString, $bindArray );
        return $zdbh->returnResult();
    }

    #########################################################
    # File System Operations
    #########################################################
    static function fileInPathCheck($file)
    {
        $path = self::getHostDir() . self::getCurrentUsername() . '/public_html/' . $file . '/';
        $realPath = realpath($path);
        
        if(!$realPath) {
            self::setFlashMessage('error', 'Path \'' . $path . '\' not found.');
            return false;
        }
        
        if( 0 !== strpos($realPath, self::getHostDir() . self::getCurrentUsername() . '/')) {
            self::setFlashMessage('error', 'Path \'' . $realPath . '\' is outside your home directory and is not allowed.');
            return false;
        }
        
        self::setFlashMessage('debug', 'fileInPathCheck successful');
        return $realPath;
    }


    #########################################################
    # Htpasswd Password Generation
    #########################################################
    
    
    #########################################################
    # Service Output methods
    #########################################################
    static function getFileList()
    {
        return self::fetchFileList( self::getCurrentUserId() );
    }
    
    static function getFile()
    {
        return array(self::fetchFile( self::getCurrentUserId() , (int) self::getId() ));
    }
    
    static function getHostDir()
    {
        return ctrl_options::GetSystemOption('hosted_dir');
    }
    
    #########################################################
    # Input Checkers
    #########################################################
    private static function getId()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ( 
            (isset($urlvars['control'])) && 
            (isset($urlvars['id'])) 
        ) {
            return (int) $urlvars['id'];
        }
        return false;
    }
    
    #########################################################
    # Post Actions
    #########################################################
    static function doCreateProtection()
    {
        global $controller;
        runtime_csfr::Protect();
        
        $file = $controller->GetControllerRequest('FORM', 'file');
        $message = $controller->GetControllerRequest('FORM', 'message');
        
        
        // Check File path security check
        if(!self::hasFlashErrors()) {
            $fileTarget = self::fileInPathCheck($file);
        }
        
        // Create DB record
        if(!self::hasFlashErrors() && $fileTarget) {
            self::createFile(
                array(
                    'x_zvps_htpasswd_file_target'    => $fileTarget,
                    'x_zvps_htpasswd_file_message'   => $message,
                    'x_zvps_htpasswd_file_created'   => time(),
                    'x_zvps_htpasswd_zpanel_user_id' => self::getCurrentUserId(),
                )
            );
        }
        
        // Create or append to .htaccess
        
        // No errors
        
        // Errors
        
    }
    
    
    #########################################################
    # Controller Actions
    #########################################################
    static function getisEditProtection()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['control'])) && ($urlvars['control'] === "EditProtection")) {
            return true;
        }
        return false;
    }

    static function getisCreateProtection()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['control'])) && ($urlvars['control'] === "CreateProtection")) {
            return true;
        }
        return false;
    }
    
    static function getisDeleteProtection()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['control'])) && ($urlvars['control'] === "DeleteProtection")) {
            return true;
        }
        return false;
    }

    static function getisEditUser()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['control'])) && ($urlvars['control'] === "EditUser")) {
            return true;
        }
        return false;
    }

    static function getisCreateUser()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['control'])) && ($urlvars['control'] === "CreateUser")) {
            return true;
        }
        return false;
    }
    
    static function getisDeleteUser()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['control'])) && ($urlvars['control'] === "DeleteUser")) {
            return true;
        }
        return false;
    }
    
    static function getisIndex()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ( 
            (!isset($urlvars['control'])) || 
            ( (isset($urlvars['control'])) && ($urlvars['control'] === "Index")) 
        ) {
            return true;
        }
        return false;
    }

    #########################################################
    # General Utility Methods
    #########################################################
    static function getCSFR_Tag()
    {
        return runtime_csfr::Token();
    }

    static function getModuleDesc()
    {
        $message = ui_language::translate( ui_module::GetModuleDescription() );
        return $message;
    }

    static function getModuleName()
    {
        $module_name = ui_language::translate( ui_module::GetModuleName() );
        return $module_name;
    }

    static function getModuleIcon()
    {
        global $controller;
        $module_icon = "modules/" . $controller->GetControllerRequest( 'URL', 'module' ) . "/assets/icon.png";
        return $module_icon;
    }

    private static function getCurrentUserId()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return $currentuser[ 'userid' ];
    }
    
    private static function getCurrentUsername()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return $currentuser[ 'username' ];
    }

    static function getFlashMessage()
    {
        return self::$flash_messanger;
    }
    
    static function setFlashMessage($type,$message)
    {
        self::$flash_messanger[] = array($type => $message);
    }
    
    static function hasFlashErrors()
    {
        $messages = self::getFlashMessage();
        
        if(empty($messages)) { return false; }
        
        foreach( $messages as $message ) {
            return array_key_exists('error', $message);
        }
        
        return true;
    }

}