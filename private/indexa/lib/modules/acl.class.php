<?

    /**
    * @name acl.class.php
    * @date 2007/05/25
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
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
        * Acl construction:
        * @name acl
        *
        */
        public function acl ( $db ) {

            $this->db = $db;

        } // end constructor

        /**
        * Loads the access controll list from db
        *
        * @name can_do
        * @param integer $gid group id
        * @param string $app module name
        * @param string $object action name
        *
        */
        public function can_do ( $gid, $app, $object ) {

            $permissions = $this->db->getOne("SELECT permissions FROM ".PERMISSIONS_TABLE." WHERE group_id = '$gid' AND app = '$app' AND object = '$object'");

            if( $permissions ) foreach( split(",",$permissions) as $p ) $this->permissions[$p] = 1;

        } // end can_do

        /**
        * Checks for read acl
        *
        * @name can_read
        *
        * @return integer 1/0
        *
        */
        public function can_read () {

                return $this->permissions["R"] ? 1 : 0;

        } // end can_read

        /**
        * Checks for write acl
        *
        * @name can_write
        *
        * @return integer 1/0
        *
        */
        public function can_write () {

                return $this->permissions["W"] ? 1 : 0;

        } // end can_write

        /**
        * Checks for delete acl
        *
        * @name can_delete
        *
        * @return integer 1/0
        *
        */
        public function can_delete () {

                return $this->permissions["D"] ? 1 : 0;

        } // end can_delete

        /**
        * Extracts permissions for a menu
        *
        * @name get_menu_list
        * @param integer $gid group id
        *
        * @return array acl list
        *
        */
        public function get_menu_list ( $gid ) {

            $acls = $this->db->getAll("SELECT * FROM ".PERMISSIONS_TABLE." WHERE group_id = '$gid'");
            foreach( $acls as $acl ){
                $permissions = split(",", $acl["permissions"] );
                foreach( $permissions as $p )
                    $acl_list[$acl["app"]][$acl["object"]][$p] = 1;
            }

            return $acl_list;

        } // end get_menu_list


        /**
        * Lists all user groups
        *
        * @name list_groups
        *
        * @return array user list
        *
        */

        public function list_groups(){

            return $this->db->getAll("SELECT * FROM ".USERGROUP_TABLE." ORDER BY name");

        } // end list_groups

        /**
        * Lists all objects in a app
        *
        * @name list_objects
        * @param integer $group_id group id
        * @param string $app_name
        * @return array object list
        */

       public function list_objects($group_id,$app_name){

            return $this->db->getAll("SELECT object, permissions FROM ".PERMISSIONS_TABLE." WHERE group_id='$group_id' AND app='$app_name' ORDER BY object");

       } // end list_objects

        /**
        * Adds an objects
        *
        * @name add_object
        * @param integer $group_id group id
        * @param string $app_name app name
        * @param string $object_name object name
        * @return void
        *
        */
        public function add_object($app_name,$group_id,$object_name){

            if (!$this->db->getOne("SELECT group_id FROM  ".PERMISSIONS_TABLE." WHERE app='$app_name' AND group_id='$group_id' AND object='$object_name'")) {

                $this->db->query("INSERT INTO ".PERMISSIONS_TABLE." SET app='$app_name',group_id='$group_id',object='$object_name',permissions='R' ");
            }

        } // end list_objects

        /**
        * Deletes an objects
        *
        * @name delete_object
        * @param integer $group_id group id
        * @param string $app_name app name
        * @param string $object_name object name
        * @return void
        *
        */
        public function delete_object($app_name,$group_id,$object_name){

            $this->db->query("DELETE FROM ".PERMISSIONS_TABLE." WHERE app='$app_name' AND group_id='$group_id' AND object='$object_name' ");

        } // end delete_object

        /**
        * Modify a right
        * on or off (w,r,d) rule
        *
        * @name modify_right
        * @param set $right R,W,D
        * @param string $app_name app name
        * @param integer $group_id group id
        * @param string $object_name object name
        * @param string $action action
        * @return void
        *
        */
        public function modify_right($right,$app_name,$group_id,$object_name,$action){


            if ($action=='add') {
                $this->db->query("UPDATE ".PERMISSIONS_TABLE." SET permissions = CONCAT(permissions,',$right')  WHERE app='$app_name' AND group_id='$group_id' AND object='$object_name' ");
            }

            if($action=='remove'){
                $this->db->query("UPDATE ".PERMISSIONS_TABLE." SET permissions = REPLACE(permissions,'$right','')  WHERE app='$app_name' AND group_id='$group_id' AND object='$object_name' ");
            }


        } // end modify_right

        /**
        * Add new group
        *
        * @name add_group
        * @param string $name group name
        * @return void
        *
        */
        public function add_group($name){

            if( !$this->db->getOne("SELECT name FROM ".USERGROUP_TABLE." WHERE name='$name'") )
                $this->db->query("INSERT INTO ".USERGROUP_TABLE." SET name='$name'");

        }//end add_group delete_group

        /**
        * Delete an existing group
        *
        * @name delete_group
        * @param int $group_id group id
        * @return void
        *
        */
        public function delete_group($group_id){

            $this->db->query("DELETE FROM ".USERGROUP_TABLE." WHERE group_id='$group_id'");
            $this->db->query("DELETE FROM ".PERMISSIONS_TABLE." WHERE group_id='$group_id'");

        } // end delete_group

        /**
        * List all apps for a group
        *
        * @name list_apps
        * @param integer $group_id group id
        * @return array $apps all apps
        *
        */
        public function list_apps($group_id){

            return $this->db->getCol("SELECT DISTINCT app as app FROM ".PERMISSIONS_TABLE." WHERE group_id='$group_id' ORDER BY app");

        } // end list_apps

        /**
        * Delete an existing app
        *
        * @name delete_group
        * @param integer $group_id group id
        * @param string $app_name app name
        * @return void
        *
        */
        public function delete_app($group_id,$app_name){

            $this->db->query("DELETE FROM ".PERMISSIONS_TABLE." WHERE group_id='$group_id' AND app='$app_name'");

        } // end delete_app

        /**
        * Adds apps
        *
        * @name add_app
        * @param string $app_name app name
        * @param integer $group_id group id
        * @return void
        *
        */
        public function add_app($app_name,$group_id){

          if (!$this->db->getOne("SELECT group_id FROM ".PERMISSIONS_TABLE." WHERE app='$app_name' AND group_id='$group_id'")) {

            $this->db->query("INSERT INTO ".PERMISSIONS_TABLE." SET app='$app_name',group_id='$group_id',object='index',permissions='R' ");

          }

        } // end add_app


        /**
        * Extracts group name by id
        *
        * @name group_name
        * @param integer $group_id group id
        * @return string group name
        *
        */
        public function group_name($group_id){

            return $this->db->getOne("SELECT name FROM ".USERGROUP_TABLE." WHERE group_id = '$group_id'");

        } // end group_name

    } // end acl

?>