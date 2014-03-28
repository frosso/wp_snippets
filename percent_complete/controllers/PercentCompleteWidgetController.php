<?php

/**
 * Class PercentCompleteWidgetController
 */
class PercentCompleteWidgetController extends MyBaseController {

    public function __construct() {
        parent::__construct(
            array(
                'views_directory' => dirname( __FILE__ ) . '/../views/',
            )
        );

        // instead of the welcome panel (unnecessary for my clients)
        // I provide them a customized "percent complete" widget, with all the actions they can do
        remove_action( 'welcome_panel', 'wp_welcome_panel' );
        add_action( 'welcome_panel', array(
            &$this,
            'view'
        ) );
    }

    public function index() {

        // remember to update the position of these. Usually I leave them in the 'models' folder
        require_once dirname( __FILE__ ) . '/../models/percent_complete/ITodoItem.php';
        require_once dirname( __FILE__ ) . '/../models/percent_complete/PercentComplete.php';
        require_once dirname( __FILE__ ) . '/../models/percent_complete/include_items.php';

        $percent_complete = PercentComplete::getInstance();

        $progress = $percent_complete->getPercentComplete();
        $items = $percent_complete->getItems();


        return array( 'index.php', compact( 'progress', 'items' ) );
    }

    /**
     * Checks if it's this controller responsibility to answer the requested URL
     *
     * @return bool
     */
    public function matches() {
        if ( !is_admin() ) {
            return false;
        }

        global $pagenow;
        if ( $pagenow != 'index.php' ) {
            return false;
        }

        return true;
    }

    public function enqueueScripts() {
        // Please, download this plugin and leave it somewhere in your website
        wp_register_script( 'jquery-knob', 'http://anthonyterrien.com/js/jquery.knob.js', array( 'jquery' ) );
        wp_enqueue_script( 'jquery-knob' );
    }
}

new PercentCompleteWidgetController();