<?php

abstract class MyAbstractCPT {

    /**
     * @var array
     */
    protected $post_args;

    /**
     * @var string
     */
    protected $template_path;

    public function __construct( array $args = array() ) {

        if ( empty( $args['post_args'] ) ) {
            $args['post_args'] = array();
        }
        $this->post_args = $args['post_args'];

        /**
         * register the custom post type
         */
        add_action( 'init', array(
            &$this,
            'registerPostType'
        ), 0 );

        /**
         * override the template
         */
        if ( !empty( $args['template_path'] ) ) {
            $this->template_path = $args['template_path'];
            add_filter( 'template_include', array(
                &$this,
                'overrideTemplate'
            ) );
        }

    }

    public function registerPostType() {
        register_post_type( self::getName(), $this->post_args );
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function overrideTemplate( $template ) {
        if ( !is_admin() ) {
            if ( is_singular( self::getNome() ) ) {
                $template = $this->template_path;
            }
        }

        return $template;
    }

    /**
     * Returns the name of the Custom Post Type
     *
     * @return string
     */
    abstract public static function getName();
} 