<?php

class MyExampleController extends MyBaseController implements ICustomMessages {

    public function __construct( array $args = array() ) {
        parent::__construct( array(
            'views_directory' => MY_PLUGIN_PATH . '/app/views/',
            'page'            => 'my_plugin-example'
        ) );

        add_action( 'admin_menu', array( &$this, 'addMenuPage' ) );
        add_action( 'wp_ajax_my_plugin_store', array( &$this, 'store' ) );
    }

    public function addMenuPage() {
        add_menu_page(
            'My Plugin',
            'My Plugin',
            'upload_files',
            $this->page,
            array(
                $this,
                'view'
            ),
            '',
            '40'
        );
    }

    public function index() {
        $some_variable = 'Hello world!';

        // we provide a view and a variable
        return array( 'example/index.php', compact( 'some_variable' ) );
    }

    /**
     * We can call this method either using ajax or vith a link
     */
    public function store() {
        // maybe we can check some capability
        if ( !current_user_can( 'install_plugins' ) ) {
            wp_die( 'WTF, Man?!?', '', array( 'response' => 403 ) );
        }

        // do stuff here

        // since we can call this method doing ajax, let's answer appropriately
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            // maybe you could also set the headers to a 201, if you're feeling fancy. But right now I'm feeling dirty. And gross.
            echo json_encode( array( 'message' => 'stored' ) );
            exit();
        }

        // otherwise, let's redirect (Wordpress usually redirects after each action,
        // so you don't always post the same thing multiple times)
        $url = admin_url( 'admin.php' );
        $url = add_query_arg( array(
            'page'    => $this->page,
            'message' => 'stored'
        ), $url );
        wp_redirect( $url );
        exit();
    }

    public function enqueueScripts() {
        // maybe we can use wp_enqueue_scripts to enqueue some js to handle
    }

    public function mergeCustomMessages( $other_messages ) {
        $other_messages['stored'] = 'Thing stored!';

        return $other_messages;
    }
}

new MyExampleController();