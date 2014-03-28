<?php

class MyPage extends MyAbstractPage {

    protected static $page_name = 'My custom page';

    protected function __construct( array $args = array() ) {
        parent::__construct( array(
            'plugin_name'   => __FILE__,
            'template_path' => dirname( __FILE__ ) . '/app/template.php'
        ) );
    }

}
// without calling it at least once, the page is never created
MyPage::getInstance();

class MoviesCPT extends MyAbstractCPT {

    public function __construct( array $args = array() ) {
        parent::__construct( array(
            'post_args' =>
                array(
                    'public' => true,
                    'labels' => array(
                        'name' => 'Movies'
                    )
                )
        ) );
    }

    /**
     * Returns the name of the Custom Post Type
     *
     * @return string
     */
    public static function getName() {
        return 'movie';
    }
}