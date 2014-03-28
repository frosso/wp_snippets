<?php

abstract class MyBaseController {
    protected $identifier = 'page';
    protected $page;
    protected $views_directory;

    protected $content;

    /**
     * You can pass some custom arguments from your class
     *
     * <code>
     * parent::__construct( array(
     *      'views_directory' => dirname(SOME_CONSTANT) . '/app/views/, // (required, the directory where your views are stored)
     *      'identifier' => 'page', // (not required)
     *      'page' => 'my_plugin_my_controller' // (required, the name that should identify this controller) )
     * );
     * </code>
     *
     * @param array $args
     */
    public function __construct( array $args = array() ) {
        $arguments = array( 'views_directory', 'identifier', 'page', );
        foreach ( $arguments as $argument ) {
            if ( isset( $args[$argument] ) ) {
                $this->$argument = $args[$argument];
            }
        }

        add_action( 'admin_init', array( $this, 'route' ) );
        if ( $this instanceof ICustomMessages ) {
            add_filter( 'frosso_plugin/messages', array( $this, 'mergeCustomMessages' ) );
        }
    }

    public abstract function enqueueScripts();

    /**
     * Checks if it's this controller responsibility to answer the requested URL
     * Extend this method at will!
     *
     * @return bool
     */
    public function matches() {
        $page = !empty( $_REQUEST[$this->identifier] ) ? $_REQUEST[$this->identifier] : null;

        if ( $page != $this->page ) {
            return false;
        }

        return true;

    }

    /**
     * Echoes the view content to the user
     * You should provide this action if you plan to ad a menu item in the wp-admin area
     */
    public function view() {
        if ( $this->matches() ) {
            echo $this->content;
        }
    }

    /**
     * Calls the right method on this controller
     */
    public function route() {
        if ( !$this->matches() ) {
            return;
        }

        $action = !empty( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'index';

        // We call the right method (if it's not provided or it doesn't exists, we call index)
        if ( !is_callable( array( $this, $action ) ) ) {
            $action = 'index';
        }

        $result = call_user_func( array( $this, $action ) );
        // $result[0] has the view file
        // $result[1] has possible variables
        if ( empty( $result[0] ) ) {
            // view not provided
            return;
        }

        // did we provide some variables to the view?
        if ( empty( $result[1] ) ) {
            $result[1] = array();
        }

        // we show the messages only in our views
        add_action( 'frosso_plugin/show_messages', array( $this, 'showMessages' ) );

        // we enqueue the scripts only in our views
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueueScripts' ) );

        $this->buildView( $result[0], $result[1] );
    }

    /**
     * Captures the view content to be echoed later with the 'view' method
     *
     * @param $view
     * @param array $params
     *
     * @throws Exception if the view is not found
     */
    private function buildView( $view, array $params = array() ) {

        $view_file = $this->views_directory . $view;

        if ( !file_exists( $view_file ) ) {
            throw new Exception( 'File not found: ' . $view_file );
        }

        ob_start();
        extract( $params );
        include_once $view_file;
        $this->content = ob_get_contents();
        ob_end_clean();

    }

    /**
     * Shows the messages on the admin header when the function
     * do_action('frosso_plugin/show_messages');
     * is called (use it in your view)
     */
    public function showMessages() {
        $messages = apply_filters( 'frosso_plugin/messages', array() );
        $message = isset( $_REQUEST['message'] ) ? $_REQUEST['message'] : null;

        if ( !empty( $messages[$message] ) ) :?>
            <div class="updated">
                <ul>
                    <li><?php echo $messages[$message]; ?></li>
                </ul>
            </div>
        <?php endif;
    }
} 