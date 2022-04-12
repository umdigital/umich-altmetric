<?php

class UmichAltmetric_Block_Altmetric
{
    static private $_prefix = 'umichaltmetric-altmetric';
    static private $_block  = 'altmetric';

    static public function init()
    {
        $script       = null;
        $styles       = null;
        $editorStyles = null;
        $editorScript = null;

        // FRONT & BACK END JS
        if( file_exists( dirname( __FILE__ ) . DIRECTORY_SEPARATOR .'script.js' ) ) {
            $script = self::$_prefix .'--'. self::$_block .'-js';

            wp_register_script(
                $script,
                plugins_url( '/script.js', __FILE__ ),
                array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-api' ),
                filemtime( dirname( __FILE__ ) . DIRECTORY_SEPARATOR .'script.js' )
            );
        }

        // FRONT & BACKEND STYLES
        if( file_exists( dirname( __FILE__ ) . DIRECTORY_SEPARATOR .'styles.css' ) ) {
            $style = self::$_prefix .'--'. self::$_block .'-css';

            wp_register_style(
                $style,
                plugins_url( '/styles.css', __FILE__ ),
                array(),
                filemtime( dirname( __FILE__ ) . DIRECTORY_SEPARATOR .'styles.css' )
            );
        }

        // BACKEND STYLES
        if( file_exists( dirname( __FILE__ ) . DIRECTORY_SEPARATOR .'editor.css' ) ) {
            $editorStyles = self::$_prefix .'--'. self::$_block .'-ed-css';

            wp_register_style(
                $editorStyles,
                plugins_url( '/editor.css', __FILE__ ),
                array(),
                filemtime( dirname( __FILE__ ) . DIRECTORY_SEPARATOR .'editor.css' )
            );
        }

        $editorScript = self::$_prefix .'--'. self::$_block .'-ed-js';
        wp_register_script(
            $editorScript,
            plugins_url( '/editor.js', __FILE__ ),
            array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-api' ),
            filemtime( dirname( __FILE__ ) . DIRECTORY_SEPARATOR .'editor.js' )
        );

        register_block_type( __DIR__, array(
            'script'          => $script,
            'style'           => $style,
            'editor_style'    => $editorStyles,
            'editor_script'   => $editorScript,
            'render_callback' => function( $instance, $content ){
                $instance = array_merge(array(
                    'url'           => null,
                    'badgeType'     => 'medium-donut',
                    'badgePosition' => 'right',
                    'limit'         => 25,
                    'template'      => 'default',
                    'className'     => 'is-style-basic'
                ), $instance );

                $classes = array();
                $classes[] = 'wp-block-umichaltmetric-altmetric';
                $classes[] = "uma-badge-type--{$instance['badgeType']}";
                $classes[] = $instance['className'];

                $instance['className'] = implode( ' ', $classes );

                $altmetricRes = UmichAltmetric::get(array(
                    'url'   => $instance['url'],
                    'limit' => $instance['limit']
                ));

                if( $altmetricRes ) {
                    ob_start();
                    $template = implode( DIRECTORY_SEPARATOR, array( UmichAltmetric::$pluginPath, 'templates', 'block-default.tpl' ) );
                    if( $tpl = locate_template( array( "umich-altmetric/block-{$instance['template']}.tpl" ), false ) ) {
                        $template = $tpl;
                    }

                    include $template;
                    return ob_get_clean();
                }
            })
        );

        add_action( 'admin_enqueue_scripts', function(){
            wp_enqueue_script('umich-altmetric-donut', 'https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js' );
        });
    }
}
UmichAltmetric_Block_Altmetric::init();
