<?php

/**
 * Namespace declaration
 */
namespace MJJ\WooProductAttributeTab;

/**
 * Exit if accessed directly
 */
defined('ABSPATH') or die();

/**
 * Dependencies
 */
require_once('trait.singleton.php');
require_once('class.meta.php');

/**
 * Handles the registering and rendering of the additional product attribute tabs.
 */
class Tabs {

    /**
     * Use Singleton trait to disallow multiple instances of this class.
     * You may also fetch the instance of this class to remove registered filter and action hooks.
     */
    use Singleton;

    /**
     * Constructs a new instance of this class and registers the required actions and filters.
     */
    protected function __construct() {
        add_filter('woocommerce_product_tabs', array($this, 'add_product_tabs'), 5);

        // allow the use of shortcodes within the tab content
        add_filter('woocommerce_product_attribute_tab_content', 'do_shortcode', 10);
    }

    /**
     * Adds the additional product tab if any of the product attributes has an additional product tab description.
     * The default name of the product tab is the name of the product attribute taxonomy.
     * Every attribute description is enclosed in a separate paragraph tag (<p>).
     * 
     * @param  array $tabs The current tabs.
     * @return The modified tabs as applicable.
     */
    public function add_product_tabs($tabs) {
        global $product;

        foreach ($product->get_attributes() as $attribute) {
            if ($attribute['is_taxonomy']) {
                $contents = array();
                $terms = wp_get_post_terms($product->id, $attribute['name']);
                foreach ($terms as $term) {
                    $tab_content = get_term_meta($term->term_id, Meta::instance()->get_meta_key(), true);
                    if ($tab_content) {
                        $contents[] = $tab_content;
                    }
                }

                if ($contents) {
                    $tabs[$attribute['name']] = array(
                        'title'    => apply_filters('woocommerce_product_attribute_tab_title', wc_attribute_label($attribute['name'], $product), $product, $attribute),
                        'priority' => apply_filters('woocommerce_product_attribute_tab_priority', 20 + $attribute['position'], $product, $attribute),
                        'callback' => array($this, 'render_tab'),
                        'content'  => implode('', array_map(function($content) {
                            return '<p>' . $content . '</p>';
                        }, $contents))
                    );
                }
            }
        }
        return $tabs;
    }


    /**
     * Renders the tab content using the given parameters.
     * 
     * @param  string $key The unique tab key.
     * @param  array $tab The tab information.
     */
    public function render_tab($key, $tab) {
        
        // allow shortcodes to function
        $content = apply_filters( 'the_content', $tab['content'] );
        $content = str_replace( ']]>', ']]&gt;', $content );

        echo apply_filters('woocommerce_product_attribute_tab_heading', '<h4>' . $tab['title'] . '</h4>', $tab);
        echo apply_filters('woocommerce_product_attribute_tab_content', $content, $tab);
    }
}

?>