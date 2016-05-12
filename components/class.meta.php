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

/**
 * This class defines, saves and prints additional product attribute meta information that are 
 * needed by this plugin to function correctly.
 */
class Meta {

    /**
     * Use Singleton trait to disallow multiple instances of this class.
     * You may also fetch the instance of this class to remove registered filter and action hooks.
     */
    use Singleton;

    /**
     * The default meta key.
     * @var string
     */
    private $meta_key = 'product-tab-description';

    /**
     * Constructs a new instance of this class and registers the required actions and filters.
     */
    private function __construct() {
        add_action('init', array($this, 'init'));
    }

    /**
     * Initialization hook that registers actions for all available product attribute taxonomies.
     */
    public function init(){

        foreach (wc_get_attribute_taxonomy_names() as $pa) {
            add_action("{$pa}_add_form_fields", array($this, 'add_field'), 10);
            add_action("${pa}_edit_form_fields", array($this, 'edit_field'), 10);
            add_action("created_{$pa}", array($this, 'save_field'), 10);
            add_action("edited_{$pa}", array($this, 'save_field'), 10);
        }
    }

    /**
     * Prints a text-area field with label for defining a product tab description of a new product attribute.
     * 
     * @param string $taxonomy The current product attribute taxonomy.
     */
    public function add_field($taxonomy = null) {
        Util::load_template('pa-add-form-field.php', array('meta_key' => $this->get_meta_key()));
    }

    /**
     * Prints a text-area field with label for editing the product tab description of an existing product attribute.
     *
     * @param stdClass $term The current $term object.
     * @param string $taxonomy The current product attribute taxonomy.
     */
    public function edit_field($term, $taxonomy = null) {
        Util::load_template('pa-edit-form-field.php', array(
            'meta_key' => $this->get_meta_key(),
            'description' => $term ? get_term_meta($term->term_id, $this->get_meta_key(), true) : ''
        ));
    }

    /**
     * Saves the product tab description in the request. The function uses the `update_term_meta` function, 
     * and can therefore be used to save new values or update existing ones.
     * 
     * @param  int $term_id ID of the term that is being saved.
     * @param  [int $term_taxonomy_id ID of the taxonomy that is being saved.
     */
    public function save_field($term_id, $term_taxonomy_id) {
        if (isset($_POST[$this->get_meta_key()]) && !empty($_POST[$this->get_meta_key()])) {
            $description = $_POST[$this->get_meta_key()];
            update_term_meta($term_id, $this->get_meta_key(), $description, true);
        }
    }

    /**
     * Gets the meta key of the additional product attribute description.
     * 
     * @return string The meta key
     */
    public function get_meta_key() {
        return apply_filters('woocommerce_product_attribute_meta_key', $this->meta_key);
    }
}

?>