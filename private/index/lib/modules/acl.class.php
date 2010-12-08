<?

    /**
    * @name acl.class.php
    * @date 2007/07/11
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    * @version 2.0
    *
    */
    class acl {
        /**
        * @var array
        */
        protected $permissions;
        /**
        * @var resource
        */
        protected $db;

        /**
        * Acl constructor
        * @name __construct
        *
        */
        public function __construct ( $kernel ) {

            $this->db = $kernel->db;

        } // end __construct

        /**
        * Loads the access control list from db
        *
        * @name can_do
        * @param string $gid group id (could be a single id or a list id1,id2 ... )
        * @param string $app module name
        * @param string $object action name
        *
        */
        public function can_do ( $gid, $app, $object ) {

            // Initializations
            $gid = $gid ? $gid : "''";
            $this->permissions = array();

            // Extract and set permissions array
            $permissions = join(",", $this->db->getCol("SELECT permissions FROM ".PERMISSIONS_TABLE." WHERE group_id IN ($gid) AND app = '$app' AND object = '$object'") );
            if( $permissions ) foreach( explode(",",$permissions) as $p ) $this->permissions[$p] = 1;

        } // end can_do

        /**
        * Checks for read acl
        * @name can_read
        *
        * @return boolean
        *
        */
        public function can_read () {

                return $this->permissions["R"] ? true : false;

        } // end can_read

        /**
        * Checks for write acl
        * @name can_write
        *
        * @return boolean
        *
        */
        public function can_write () {

                return $this->permissions["W"] ? true : false;

        } // end can_write

        /**
        * Checks for delete acl
        * @name can_delete
        *
        * @return boolean
        *
        */
        public function can_delete () {

                return $this->permissions["D"] ? true : false;

        } // end can_delete

        /**
        * Extracts all permissions for a menu
        *
        * @name get_menu_list
        * @param string $gid group id (could be a single id or a list id1,id2 ... )
        *
        * @return array acl list
        *
        */
        public function get_menu_list ( $gid ) {

            $gid = $gid ? $gid : "''";
            $acls = $this->db->getAll("SELECT * FROM ".PERMISSIONS_TABLE." WHERE group_id IN ($gid)");

            foreach( $acls as $acl )
                foreach( explode(",", $acl["permissions"] ) as $p )
                    $acl_list[$acl["app"]][$acl["object"]][$p] = 1;

            return $acl_list;

        } // end get_menu_list

    } // end acl

?>