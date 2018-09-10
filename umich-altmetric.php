<?php
/**
 * Plugin Name: University of Michigan: Altmetric
 * Plugin URI: https://github.com/umdigital/umich-altmetric/
 * Description: Display Altmetric attention lists via API
 * Version: 1.2.1
 * Author: U-M: Digital
 * Author URI: http://vpcomm.umich.edu
 */

define( 'UMICHALTMETRICS_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );

class UmichAltmetric {
    static public $displayCounter = 0;

    static private $_cacheTimeout = 6;

    static public function init()
    {
        self::$_cacheTimeout = 60 * 60 * (self::$_cacheTimeout >= 1 ? self::$_cacheTimeout : 1);

        // UPDATER SETUP
        if( !class_exists( 'WP_GitHub_Updater' ) ) {
            include_once UMICHALTMETRICS_PATH .'includes'. DIRECTORY_SEPARATOR .'updater.php';
        }
        if( isset( $_GET['force-check'] ) && $_GET['force-check'] && !defined( 'WP_GITHUB_FORCE_UPDATE' ) ) {
            define( 'WP_GITHUB_FORCE_UPDATE', true );
        }
        if( is_admin() ) {
            new WP_GitHub_Updater(array(
                // this is the slug of your plugin
                'slug' => plugin_basename(__FILE__),
                // this is the name of the folder your plugin lives in
                'proper_folder_name' => dirname( plugin_basename( __FILE__ ) ),
                // the github API url of your github repo
                'api_url' => 'https://api.github.com/repos/umdigital/umich-altmetric',
                // the github raw url of your github repo
                'raw_url' => 'https://raw.githubusercontent.com/umdigital/umich-altmetric/master',
                // the github url of your github repo
                'github_url' => 'https://github.com/umdigital/umich-altmetric',
                 // the zip url of the github repo
                'zip_url' => 'https://github.com/umdigital/umich-altmetric/zipball/master',
                // wether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
                'sslverify' => true,
                // which version of WordPress does your plugin require?
                'requires' => '3.0',
                // which version of WordPress is your plugin tested up to?
                'tested' => '3.9.1',
                // which file to use as the readme for the version number
                'readme' => 'README.md',
                // Access private repositories by authorizing under Appearance > Github Updates when this example plugin is installed
                'access_token' => '',
            ));
        }

        add_action( 'wp_enqueue_scripts', function(){
            // load altmetric donut
            wp_enqueue_script('umich-altmetric-donut', 'https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js' );

            wp_enqueue_style( 'umich-altmetric', plugins_url('assets/umich-altmetric.css', __FILE__ ) );
        });

        add_shortcode( 'altmetric', function( $atts, $url = '' ){
            self::$displayCounter++;

            $atts = shortcode_atts(array(
                'url'        => '',
                'limit'      => 25,
                'donut-size' => 'medium',
                'template'   => 'default',
            ), $atts );

            $atts['donut-size'] = $atts['donut-size'] == 'small' ? '' : $atts['donut-size'];

            $url = htmlspecialchars_decode( urldecode( trim( strip_tags( $url ) ) ) );
            if( $url && filter_var( $url, FILTER_VALIDATE_URL ) ) {
                $atts['url'] = $url;
            }

            // normalize url
            $atts['url'] = htmlspecialchars_decode( urldecode( $atts['url'] ) );

            // add limit "page[size]=#" to url
            $parts = parse_url( urldecode( $atts['url'] ) );
            parse_str( $parts['query'], $parts['query'] );
            $parts['query']['page']['size'] = $atts['limit'] ?: 1;
            $parts['query'] = http_build_query( $parts['query'] );
            $parts['query'] = $parts['query'] ? '?'. $parts['query'] : '';
            $atts['url'] = "{$parts['scheme']}://{$parts['host']}{$parts['path']}{$parts['query']}";

            // locate template
            $tpl = implode( DIRECTORY_SEPARATOR, array( UMICHALTMETRICS_PATH, 'templates', 'default.tpl' ) );
            $tpl = locate_template( array( 'umich-altmetric/'. $atts['template'] .'.tpl' ), false ) ?: $tpl;

            // GET ALTMETRICS DATA
            $wpUpload  = wp_upload_dir();
            $cachePath = implode( DIRECTORY_SEPARATOR, array(
                $wpUpload['basedir'],
                'umich-altmetric-cache',
                md5( $atts['url'] ) .'.cache'
            ));

            if( !file_exists( $cachePath ) || ((@filemtime( $cachePath ) + self::$_cacheTimeout) < time()) ) {
                // update timestamp so other request don't make a pull request at the same time
                @touch( $cachePath );

                // get live results
                $stream = stream_context_create(array(
                    'http' => array(
                        'timeout' => 1
                    )
                ));
                if( $json = file_get_contents( $atts['url'], false, $stream ) ) {
                    if( $res = @json_decode( $json ) ) {
                        if( @$res->meta->response->status == 'ok' ) {
                            // CACHE RESULTS
                            wp_mkdir_p( dirname( $cachePath ) );

                            @file_put_contents( $cachePath, $json );
                        }
                    }
                }
            }

            if( $resItems = @json_decode( file_get_contents( $cachePath ) ) ) {
                $researchItems = array(
                    'items'         => array(),
                    'relationships' => array()
                );

                if( $resItems->included ) {
                    foreach( $resItems->included as $item ) {
                        $researchItems['relationships'][ $item->type ][ $item->id ] = $item->attributes;
                    }
                }

                if( $resItems->data ) {
                    foreach( $resItems->data as $item ) {
                        $researchItems['items'][ $item->id ] = $item;
                    }
                }

                // DISPLAY DATA
                ob_start();
                include( $tpl );
                return ob_get_clean();
            }
        });

        // 10% chance of cleanup
        if( mt_rand( 1, 10 ) == 3 ) {
            $expires = 60 * 60 * 24 * 7; // 7 days

            $wpUpload  = wp_upload_dir();
            $cachePath = implode( DIRECTORY_SEPARATOR, array(
                $wpUpload['basedir'],
                'umich-altmetric-cache',
                '*'
            ));

            foreach( glob( $cachPath ) as $file ) {
                if( (filemtime( $file ) + $expires) < time() ) {
                    unlink( $file );
                }
            }
        }
    }
}
UmichAltmetric::init();
