<?php

/**
 * Class MyAbstractPage implements GOF's Singleton.
 * I know it's a bad pattern, but if you think about it
 * the name of a page in the database must be unique.
 * So should the instance of this class.
 */
abstract class MyAbstractPage {
    /**
     * @var string
     */
    protected $template_path;

    /**
     * The plugin we have to listen to activation
     * When the plugin is activated, we create the page
     *
     * @var string
     */
    protected $plugin_name;

    /**
     * Page post data
     * @var array
     */
    protected $page_data = array();

    /**
     * @var bool
     */
    protected $delete_on_deactivation = false;

    /**
     * @var int|null
     */
    private $page_id = null;

    /**
     * Singleton instances
     *
     * @var array
     */
    protected static $instances = array();

    /**
     * You can pass some custom arguments from your class
     *
     * <code>
     * parent::__construct( array(
     *      'plugin_name' => SOME_CONSTANT,
     *      'template_path' => dirname(SOME_CONSTANT) . '/app/views/my-page.php',
     *      'page_data' => array('post_status' => 'draft'),
     *      'delete_on_deactivation' => true )
     * );
     * </code>
     *
     * @param array $args
     */
    protected function __construct( array $args = array() ) {
        $arguments = array( 'plugin_name', 'template_path', 'page_data', 'delete_on_deactivation' );
        foreach ( $arguments as $argument ) {
            if ( isset( $args[$argument] ) ) {
                $this->$argument = $args[$argument];
            }
        }

        if ( !empty( $this->plugin_name ) ) {
            register_activation_hook( $this->plugin_name,
                array(
                    get_class( $this ),
                    'activation'
                )
            );

            if ( $this->delete_on_deactivation == true ) {
                register_deactivation_hook( $this->plugin_name,
                    array(
                        get_class( $this ),
                        'deactivation'
                    )
                );
            }
        }

        /**
         * Override the template
         */
        if ( !empty( $this->template_path ) ) {
            add_filter( 'template_include', array(
                &$this,
                'overrideTemplate'
            ) );
            add_filter( 'page_template', array(
                &$this,
                'overrideTemplate'
            ), 1 );
        }

    }

    /**
     * @return MyAbstractPage
     */
    public static function getInstance() {
        $class = get_called_class();

        if ( empty( self::$instances[$class] ) ) {
            self::$instances[$class] = new $class();
        }

        return self::$instances[$class];
    }

    /**
     * Function called on plugin activation
     *
     * @return int
     */
    public static function activation() {
        return static::getInstance()->insertPage();
    }

    /**
     * @return mixed
     */
    public static function deactivation() {
        $page_id = static::getId();

        return wp_delete_post( $page_id, true );
    }

    /**
     * You can override this method if you need another post name
     *
     * @param bool $sanitize
     *
     * @return string
     */
    public static function getPageName( $sanitize = false ) {
        return $sanitize ? sanitize_title( static::$page_name ) : static::$page_name;
    }

    /**
     * @return int on success
     */
    function insertPage() {

        // check admin_id
        // $admin_id = get_user_by( 'email', get_blog_option( 1, 'admin_email' )
        // )->ID;

        // this is fine in most cases
        $admin_id = get_current_user_id();

        $page_data = array(
            'comment_status' => 'closed', // 'closed' means no comments.
            'ping_status'    => 'closed',
            'post_author'    => $admin_id,
            'post_content'   => '',
            'post_name'      => static::getPageName( true ),
            'post_status'    => 'publish',
            'post_title'     => static::getPageName(),
            'post_type'      => 'page',
        );
        $page_data = array_merge_recursive( $page_data, $this->page_data );

        // let's check if the page exists
        $page_id = $this->getPageId();
        if ( !is_null( $page_id ) ) {
            $page_data['ID'] = $page_id;
        }

        $page_id = wp_insert_post( $page_data );
        if ( $page_id != false ) {
            update_post_meta( $page_id, '_frosso_page_template', static::getMyTemplateName() );
        }

        return $page_id;
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function overrideTemplate( $template ) {
        $template_file_path = $this->template_path;

        // we do not want this in wp-admin
        if ( is_admin() ) {
            return $template;
        }

        if ( !is_page( static::getPageName( true ), static::getPageName( false ), static::getId() ) ) {
            return $template;
        }

        // assign the value and return it
        return $template = $template_file_path;
    }

    /**
     * @return int on succcess
     */
    protected function getPageId() {
        // let's see if we have it stored from a previous query
        if ( $this->page_id !== null ) {
            return $this->page_id;
        }

        // it's not stored
        // or we didn't find it before
        $query = new WP_Query();
        $pages = $query->query( array(
            'meta_query' => array(
                array(
                    // we cannot use _wp_page_template, because for some reason it gets overwritten
                    'key'     => '_frosso_page_template',
                    'value'   => static::getMyTemplateName(),
                    'compare' => '=',
                )
            ),
            // 'post_status' => 'publish',
            'post_type'  => 'page',
        ) );

        foreach ( $pages as $page ) {
            /* @var $page WP_Post */

            // applies filters. useful in case of plugins like WPML are installed
            $this->page_id = apply_filters( 'frosso_plugin/get_post_id', $page->ID );

            return $this->page_id;
        }

        return null;
    }

    /**
     * @return int on succcess
     */
    public static function getId() {
        return static::getInstance()->getPageId();
    }

    /**
     * @return string
     */
    protected static function getMyTemplateName() {
        return '_overwritten-' . static::getPageName( true ) . '.php';
    }
} 