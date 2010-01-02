<?

    function index_index (){
        global $main;

        $apps = $main->list_apps();
        print_r( $apps );
        foreach( $apps as $app )
            print_r( $main->list_objects( $app ) );

    } // end  index_index


    function index_arch(){
        $tar = (`tar --help 2>&1`) ? 1 : 0;
        $zip = (`zip --help 2>&1`) ? 1 : 0;

        if( $tar ){
            $name = "arch_".date("Y_m_d_h_i").".tgz";
            $cmd = "tar zcvf $name ./";
        } elseif ( $zip ) {
            $name = "arch_".date("Y_m_d_h_i").".zip";
            $cmd = "zip -r $name ./*";
        } else {
            echo "Unable to archive! No archive command found!\n";
            return;
        }

        echo `$cmd 2>&1`;
    }

    function index_create_app () {
        global $main;
        extract( $main->params );

        // Create app
        if( $apps ){

            $apps = split(",", $apps );
            foreach( $apps as $app )
                create_app( $app, $main->params );

        } else {

            // Display help
            echo "Help: -apps app_name (-author \"Fist last\")\n";

        }

    } // end index_create_app

    function create_app ( $app, $params ){

        if( file_exists("./private/{$app}") ){
            echo "Application {$app} exists!\n";
            return;
        }

        echo "Building application $app!\n";

        // Copy skeleton in new application
        echo `cp ./private/index/usr/skeleton/ ./private/{$app} -a -v`;
        $files = split("\n",trim(`ls ./private/{$app}/bin/*`));  //*/
        $date = date("m\\\/d\\\/Y");
        if( $params["author"] ) $author =  " -e 's/@author/@author {$params["author"]}/' ";

        // Rename files in bin and replace strings in each file
        foreach( $files as $file ){
            $file = trim($file);
            $dir = dirname($file);
            $names = split("\.", basename($file) );

            // Set up the bin file
            echo "sed -e 's/skeleton/{$app}/g' -e 's/@date/@date $date/' $author -e 's/index.tpl/{$app}.tpl/' $file > $dir/{$app}.$names[1].php\n";
            `sed -e 's/skeleton/{$app}/g' -e 's/@date/@date $date/' $author -e 's/index.tpl/{$app}.tpl/' $file > $dir/{$app}.$names[1].php`;
            // Set up the template file
            echo "sed -e 's/Skeleton/{$app}/g' ./private/{$app}/usr/templates/$names[1]/index.tpl > ./private/{$app}/usr/templates/$names[1]/{$app}.tpl\n";
            `sed -e 's/Skeleton/{$app}/g' ./private/{$app}/usr/templates/$names[1]/index.tpl > ./private/{$app}/usr/templates/$names[1]/{$app}.tpl`;
            echo "rm  ./private/{$app}/usr/templates/$names[1]/index.tpl\n";
            `rm  ./private/{$app}/usr/templates/$names[1]/index.tpl`;
        } // foreach

        // Cleanup old skeleton files
        echo "rm ./private/{$app}/bin/skeleton.*\n";
        `rm ./private/{$app}/bin/skeleton.*`;

    } // end create_app


    function index_arch_db () {

        $dumptool = `mysqldump --help 2>&1` ? 1 : 0;

        if( $dumptool ) `mysqldump unidesk -u --password= --lock_tables=false --add-drop-database=true --databases=true > ./private/index/usr/db/database.sql`;

    } // end index_arch_db


    function index_gtk (){
        global $main;

        dl("php_gtk2.so");

        // ENVIRONMENT CHECK
        if (!class_exists("gtk")) {
            die("The PHP-GTK2 class was not detected "
            . "and therefore this file cannot continue");
            }

        // ENVIRONMENT CHECK

        // CREATE THE INITIAL WINDOW WIDGET
        $window = new GtkWindow();

        //NAME THE WIDGET
        $window->set_title("Dialog Window");

        // NOW FOR SOME AESTHETICS (SCREEN POSITION FOR THE WIDGET)
        $window->set_position('GTK_WIN_POS_CENTER');

        // NOW FOR SOME AESTHETICS (INITIAL SIZE OF THE WIDGET)
        $window->set_size_request(300, 200);

        //DECLARE A BUTTON WIDGET
        $button = new GtkButton('Press');

        /**
        * LINK THE BUTTON WIDGET TO THE SPECIFIED
        * SIGNAL HANDLER (CALL_BACK METHOD/FUNCTION)
        */
        $button->connect('clicked', 'fileDialog');

        /**
        * ADD THE BUTTON WIDGET TO THE
        * WINDOW WIDGET (WHICH NOW BECOMES
        * A CONTAINER OF WIDGETS)
        */
        $window->add($button);

        // CREATE THE INITIAL WINDOW

        /* CREATE THE CALL_BACK FUNCTION
        * FOR THE SIGNAL HANDLER
        */
        function fileDialog() {
            // OPEN THE FILE DIALOG BOX
            $fileBox = new GtkFileSelection('File Box');
            
            // CREATE THE OKAY BUTTON ON THE FILE DIALOG BOX
            $ok_button = $fileBox->ok_button;
        
            /**
            * LINK THE BUTTON WIDGET TO THE SPECIFIED SIGNAL
            * HANDLER (CALL_BACK METHOD/FUNCTION)
            */
            $ok_button->connect('clicked', 'destroy');
                
            // CREATE THE CANCEL BUTTON ON THE FILE DIALOG BOX
            $cancel_button = $fileBox->cancel_button;
        
            /**
            * LINK THE BUTTON WIDGET TO THE SPECIFIED
            * SIGNAL HANDLER (CALL_BACK METHOD/FUNCTION)
            */
            $cancel_button->connect('clicked', 'destroy');
        
            $fileBox->show();
        }
        /**
        * CREATE THE CALL_BACK FUNCTION FOR
        * THE SIGNAL HANDLER
        */
        function destroy()  {
            gtk::main_quit();
        }

        /**
        * CREATE THE CALL_BACK FUNCTION
        * FOR THE SIGNAL HANDLER
        */

        /**
        * SHOW THE WINDOW WIDGET
        * (WHICH TECHNICALLY BECAME A CONTAINER
        * AFTER WE ADDED THE BUTTON WIDGET)
        */
        $window->show_all();

        /**
        * FOR LACK OF BETTER TERMINOLOGY; WE'RE
        * TELLING THE SCRIPT TO STOP HERE AND
        * MONITOR THE ACTIVE WIDGETS/CONTAINERS
        * FOR SIGNALS TO PASS TO CALL_BACK
        * FUNCTION AND/OR METHODS
        */

        Gtk::main();


    } // end  index_index


    function index_gtk2 (){
        global $main;

        dl("php_gtk2.so");
class App {

    function App() {
        $this->modules = array("sales", "purchase", "inventory", "finance"); // names of the modules
    }

    function main() {
        foreach($this->modules as $module) {
            $this->apps[$module] = new $module($this);
        }
        $this->apps['sales']->dialog->show_all(); // show the first module
        $this->apps['sales']->dialog->run(); // let's go!
    }

    // process button click
    function on_clicked($button, $activated_module) {
        print "button_clicked: $activated_module\n";
        $this->apps[$activated_module]->dialog->show_all(); // show the activated module
        foreach($this->modules as $module) {
            if ($module!=$activated_module) $this->apps[$module]->dialog->hide_all(); // hide all others
        }
        $this->apps[$activated_module]->dialog->run();
    }
}

class base_module {

    function base_module($obj) {
        $this->main = $obj; // keep a copy of the module names
        $module = get_class($this);
        print "base_module::module = $module\n";
        $dialog = new GtkDialog($module, null, Gtk::DIALOG_MODAL);
        $dialog->set_position(Gtk::WIN_POS_CENTER_ALWAYS);
        $dialog->set_size_request(400, 250);
        $top_area = $dialog->vbox;

        // run the setup for each module
        $this->setup($top_area);

        $top_area->pack_start(new GtkLabel()); // used to "force" the button to stay at bottom
        $this->show_buttons($top_area); // shows the buttons at bottom of windows
        $dialog->set_has_separator(false);
        $this->dialog = $dialog; // keep a copy of the dialog ID
    }

    // shows the buttons at bottom of windows
    function show_buttons($vbox) {
        global $modules;
        $hbox = new GtkHBox();
        $vbox->pack_start($hbox, 0, 0);
        $hbox->pack_start(new GtkLabel());
        foreach($this->main->modules as $module) {
            $button = new GtkButton(strtoupper(substr($module,0,1)).substr($module,1)); // cap 1st letter
            $button->set_size_request(80, 32); // makes all button the same size
            if ($module == get_class($this)) { // sets the color of the respective button
                $button->modify_bg(Gtk::STATE_NORMAL, GdkColor::parse("#95DDFF"));
                $button->modify_bg(Gtk::STATE_ACTIVE, GdkColor::parse("#95DDFF"));
                $button->modify_bg(Gtk::STATE_PRELIGHT, GdkColor::parse("#95DDFF"));
            }
            $hbox->pack_start($button, 0, 0);
            $hbox->pack_start(new GtkLabel());
            $button->connect('clicked', array(&$this->main,'on_clicked'), $module); // event handler to handle button click
        }
    }
}

class sales extends base_module {
    function setup($vbox) {
        // display title
        $title = new GtkLabel("Display 2D Array in GtkTreeView - Part 5\n                       get user selection");
        $title->modify_font(new PangoFontDescription("Times New Roman Italic 10"));
        $title->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#0000ff"));
        $title->set_size_request(-1, 40);
        $vbox->pack_start($title, 0, 0);

        $vbox->pack_start(new GtkLabel(), 0, 0); // add a small gap between the title and scroll_win

        // Set up a scroll window
        $scrolled_win = new GtkScrolledWindow();
        $scrolled_win->set_policy( Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
        $vbox->pack_start($scrolled_win);

        // the 2D table
        $data = array(
        array('row0', 'item 19', 2, 3.1),
        array('row1', 'item 16', 20, 6.21),
        array('row2', 'item 13', 8, 9.36),
        array('row3', 'item 10', 11, 12.4),
        array('row4', 'item 7', 5, 15.5),
        array('row5', 'item 4', 17, 18.6),
        array('row6', 'item 3', 20, 21.73));

        $this->display_table($scrolled_win, $data);
    }

    function display_table($scrolled_win, $data) {
        // Creates the list store
        $model = new GtkListStore(Gtk::TYPE_STRING, Gtk::TYPE_STRING,
        Gtk::TYPE_LONG, Gtk::TYPE_DOUBLE);
        $field_header = array('Row #', 'Description', 'Qty', 'Price');
        $field_justification = array(0, 0, 0.5, 1);

        // Creates the view to display the list store
        $view = new GtkTreeView($model);
        $scrolled_win->add($view);

        // Creates the columns
        for ($col=0; $col<count($field_header); ++$col) {
            $cell_renderer = new GtkCellRendererText();
            $cell_renderer->set_property("xalign", $field_justification[$col]);
            $column = new GtkTreeViewColumn($field_header[$col], $cell_renderer, 'text', $col);
            $column->set_alignment($field_justification[$col]);
            $column->set_sort_column_id($col);

            // set the header font and color
            $label = new GtkLabel($field_header[$col]);
            $label->modify_font(new PangoFontDescription("Arial Bold"));
            $label->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#0000FF"));
            $column->set_widget($label);
            $label->show();

            // setup self-defined function to display alternate row color
            $column->set_cell_data_func($cell_renderer, array(&$this, "format_col"), $col);
            $view->append_column($column);
        }

        // pupulates the data
        for ($row=0; $row<count($data); ++$row) {
            $values = array();
            for ($col=0; $col<count($data[$row]); ++$col) {
                $values[] = $data[$row][$col];
            }
            $model->append($values);
        }

        $selection = $view->get_selection();
        $selection->connect('changed', array(&$this, 'on_selection'));
    }

    function format_col($column, $cell, $model, $iter, $col_num) {
        $path = $model->get_path($iter);
        $row_num = $path[0];
        if ($col_num==3) {
            $amt = $model->get_value($iter, 3);
            $cell->set_property('text', '$'.number_format($amt,2));
        }
        $row_color = ($row_num%2==1) ? '#dddddd' : '#ffffff';
        $cell->set_property('cell-background', $row_color);
    }

    function on_selection($selection) {
        list($model, $iter) = $selection->get_selected();
        $desc = $model->get_value($iter, 1);
        $qty = $model->get_value($iter, 2);
        $price = $model->get_value($iter, 3);
        print "You have selected $desc: $qty ($price)\n";
    }
}

class purchase extends base_module {
    function setup($vbox) {
        // display title
        $title = new GtkLabel("Setup and read value from ComboBoxEntry");
        $title->modify_font(new PangoFontDescription("Times New Roman Italic 10"));
        $title->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#0000ff"));
        $title->set_size_request(-1, 60);
        $vbox->pack_start($title, 0, 0);

        // the selection
        $list = array('item 1', 'item 2', 'item 3', 'item 4');

        $vbox->pack_start($hbox=new GtkHBox(), 0, 0);
        $hbox->pack_start(new GtkLabel('Select: '), 0, 0);

        // Create a new comboboxentry and populates it
        $combobox = GtkComboBoxEntry::new_text();
        foreach($list as $choice) {
            $combobox->append_text($choice);
        }
        $combobox->get_child()->set_text('');
        $hbox->pack_start($combobox, 0, 0);

        // Set up the submit butotn
        $hbox->pack_start($button = new GtkLabel('  '), 0, 0);
        $hbox->pack_start($button = new GtkButton('Submit'), 0, 0);
        $button->set_size_request(60, 24);
        $button->connect('clicked', array(&$this, "on_button"), $combobox);
    }

    function on_button($button, $combobox) {
        $selection = $combobox->get_child()->get_text();
        print "You have selected: $selection\n";
    }
}

class inventory extends base_module {
    function setup($vbox) {
        // display title
        $title = new GtkLabel("Default Button Action");
        $title->modify_font(new PangoFontDescription("Times New Roman Italic 10"));
        $title->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#0000ff"));
        $title->set_size_request(-1, 40);
        $vbox->pack_start($title, 0, 0);

        $hbox = new GtkHBox();
        $vbox->pack_start($hbox, 0, 0);
        $hbox->pack_start(new GtkLabel("Keyword: "), 0, 0);
        $hbox->pack_start($entry = new GtkEntry(), 0, 0);
        $hbox->pack_start($button = new GtkButton("Search"), 0, 0);

        $entry->connect('activate', array(&$this,'on_enter'), $button);
        $button->connect('clicked', array(&$this,'on_click'), $entry);
    }

    function on_enter($entry, $button) {
        $keyword = $entry->get_text();
        echo "Enter pressed. keyword = $keyword\n";
        $button->clicked();
    }

    function on_click($button, $entry) {
        $keyword = $entry->get_text();
        echo "button clicked. keyword = $keyword\n";
    }
}

class finance extends base_module {
    function setup($vbox) {
        // display title
        $title = new GtkLabel("Display Alert - Part 1");
        $title->modify_font(new PangoFontDescription("Times New Roman Italic 10"));
        $title->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#0000ff"));
        $title->set_size_request(-1, 40);
        $vbox->pack_start($title, 0, 0);
        $vbox->pack_start(new GtkLabel(), 0, 0); // add a small gap

        // setup the entry field
        $hbox = new GtkHBox();
        $vbox->pack_start($hbox, 0, 0);
        $hbox->pack_start(new GtkLabel("Please enter your name:"), 0, 0);
        $name = new GtkEntry();
        $hbox->pack_start($name, 0, 0);
        $name->connect('activate', array(&$this,'on_activate'));
    }

    function on_activate($widget) {
        $input = $widget->get_text();
        echo "name = $input\n";
        if ($input=='') $this->alert("Please enter your name!");
        $widget->grab_focus();
    }

    // display popup alert box
    function alert($msg) {
        $dialog = new GtkDialog('Alert', null, Gtk::DIALOG_MODAL);
        $dialog->set_position(Gtk::WIN_POS_CENTER_ALWAYS);
        $top_area = $dialog->vbox;
        $top_area->pack_start($hbox = new GtkHBox());
        $stock = GtkImage::new_from_stock(Gtk::STOCK_DIALOG_WARNING,
        Gtk::ICON_SIZE_DIALOG);
        $hbox->pack_start($stock, 0, 0);
        $hbox->pack_start(new GtkLabel($msg));
        $dialog->add_button(Gtk::STOCK_OK, Gtk::RESPONSE_OK);
        $dialog->set_has_separator(false);
        $dialog->show_all();
        $dialog->run();
        $dialog->destroy();
    }

}


$app = new App();
$app->main();
}

?>