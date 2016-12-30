<?php

if(!defined('WPINC')) // MUST have WordPress.
    exit('Do NOT access this file directly: '.basename(__FILE__));


/**
 * The Forum Class
 */
class TheForum
{

  function __construct()
  {
    add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );
    add_shortcode( 'qa_forum', array($this, 'qa_forum_func') );
    add_action( 'init', array($this, 'qa_acordion_forum_post_type_func'), 0 );

    add_action( 'wp_ajax_request_forum_data', array($this, 'request_forum_data_func') );
    add_action( 'wp_ajax_nopriv_request_forum_data', array($this, 'request_forum_data_func') );

    add_action( 'wp_ajax_request_forum_comments', array($this, 'request_forum_comments_func') );
    add_action( 'wp_ajax_nopriv_request_forum_comments', array($this, 'request_forum_comments_func') );

    add_action( 'wp_ajax_replying_forum_thread', array($this, 'replying_forum_thread_func') );
    add_action( 'wp_ajax_nopriv_replying_forum_thread', array($this, 'replying_forum_thread_func') );

    add_action( 'wp_ajax_adding_forum_thread', array($this, 'adding_forum_thread_func') );
    add_action( 'wp_ajax_nopriv_adding_forum_thread', array($this, 'adding_forum_thread_func') );


    add_shortcode( 'qa_forum_add_topic', array($this, 'qa_forum_add_topic_func') );

  }


  public function adding_forum_thread_func() {

    if (empty($_POST['post_title']) || empty($_POST['post_content']))
      wp_die();

      ///
      $user_id = get_current_user_id();

      $publish_stat = (int) $_POST['publish_stat'];

      $my_post = array(
        'post_title'    => wp_strip_all_tags( $_POST['post_title'] ),
        'post_content'  => $_POST['post_content'],
        'post_status'   => ( empty($publish_stat) ? "draft" : "publish" ) ,
        'post_author'   => $user_id,
        'post_type'     => 'qa_acordion_forum_po'
      );

      // Insert the post into the database
      $insert_post = wp_insert_post( $my_post );

      if ($insert_post)
        echo "Success";
      else
        wp_die();
      ///

      wp_die();

   }

  public function replying_forum_thread_func() {

    if (!is_user_logged_in())
      wp_die();

    if (empty($_POST['post_id']))
      wp_die();

      $post_id = $_POST['post_id'];
      $current_user_id = get_current_user_id();
      $userData = get_userdata($current_user_id);
      $userName = $userData->user_login;

////

$comment_text = "";

if (!empty($_POST['replying_to_user']))
  $comment_text .= "<span><em>@".$_POST['replying_to_user'].", </em></span>";

$comment_text .= $_POST['reply_val'];

$commentdata = array(
	'comment_post_ID' => $post_id, // to which post the comment will show up
	'comment_author' => $userName, //fixed value - can be dynamic
	'comment_content' => $comment_text, //fixed value - can be dynamic
	'comment_type' => '', //empty for regular comments, 'pingback' for pingbacks, 'trackback' for trackbacks
	'comment_parent' => 0, //0 if it's not a reply to another comment; if it's a reply, mention the parent comment ID here
	'user_id' => $current_user_id, //passing current user ID or any predefined as per the demand
);

//Insert new comment and get the comment ID
$comment_id = wp_new_comment( $commentdata );

///


    echo json_encode($comment_id);

    wp_die();

  }

  public function request_forum_comments_func() {

    if (empty($_POST['post_id']))
      wp_die();

    $post_id = $_POST['post_id'];

    $data = get_comments( array('post_id' => $post_id) );

    echo json_encode($data);

    wp_die();

  }


public function qa_forum_add_topic_func($atts) {

  ob_start();

  $opt = shortcode_atts( array(
      'publish' => 0,
  ), $atts );

    $publish = $opt['publish'];

    include accordion_qa_forum_PLUGIN_DIR."template".DS."structure_topic_add.php";

    $output = ob_get_clean();


    return $output;
}


  public function request_forum_data_func() {


    $args = array(
      'numberposts' => -1,
      'post_type'   => 'qa_acordion_forum_po'
    );

    $forum_topic_posts = get_posts( $args );

    //var_dump($forum_topic_posts);

    $titles = array();
    $main_contents = array();

    foreach ($forum_topic_posts as $key => $forum_topic_post) {
      $titles[] = $forum_topic_post->post_title;
      $main_contents[] = $forum_topic_post->post_content;
    }

    $return_data = array('titles' => $titles, 'main_contents' => $main_contents, 'forum_topic_posts' => $forum_topic_posts );

    echo json_encode($return_data);

    wp_die();

  }

  public function enqueue_scripts() {

    $user_id = get_current_user_id();


    wp_register_script( 'accordion-qa-forum-general-script', accordion_qa_forum_PLUGIN_URL.'js/script.js', array( 'jquery' ), '', false );
    wp_localize_script( 'accordion-qa-forum-general-script', 'plugin_data', array( 'ajax_url' => admin_url('admin-ajax.php'), 'plugins_url' => $plugins_url ));
    wp_register_style( 'accordion-qa-forum-bootstrap-style', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css' );
    wp_register_style( 'accordion-qa-forum-jquery_ui-style', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/blitzer/jquery-ui.min.css' );

    wp_register_style( 'accordion-qa-forum-general-style', accordion_qa_forum_PLUGIN_URL.'css/style.css' );


    wp_enqueue_script( 'jquery-ui-accordion' );
    wp_enqueue_script( 'accordion-qa-forum-general-script' );
    wp_enqueue_style( 'accordion-qa-forum-bootstrap-style' );
    wp_enqueue_style( 'accordion-qa-forum-general-style' );
    wp_enqueue_style( 'accordion-qa-forum-jquery_ui-style' );


  }

  public function qa_forum_func($atts) {

    ob_start();

    // $a = shortcode_atts( array(
    //     'foo' => 'something',
    //     'bar' => 'something else',
    // ), $atts );

    include accordion_qa_forum_PLUGIN_DIR."template".DS."structure.php";

    $output = ob_get_clean();

    return $output;

  }


  // Register Custom Post Type
  function qa_acordion_forum_post_type_func() {

  	$labels = array(
  		'name'                  => _x( 'Forum Topics', 'Post Type General Name', 'accordion_qa_forum' ),
  		'singular_name'         => _x( 'Q/A Forum', 'Post Type Singular Name', 'accordion_qa_forum' ),
  		'menu_name'             => __( 'All Forum Topics', 'accordion_qa_forum' ),
  		'name_admin_bar'        => __( 'QA Forum Topic', 'accordion_qa_forum' ),
  		'archives'              => __( 'Forum Topics Archives', 'accordion_qa_forum' ),
  		'attributes'            => __( 'Forum Topics Attributes', 'accordion_qa_forum' ),
  		'parent_item_colon'     => __( 'Parent Forum Topics:', 'accordion_qa_forum' ),
  		'all_items'             => __( 'All Forum Topics', 'accordion_qa_forum' ),
  		'add_new_item'          => __( 'Add New Forum Topic', 'accordion_qa_forum' ),
  		'add_new'               => __( 'Add Forum Topic', 'accordion_qa_forum' ),
  		'new_item'              => __( 'New Forum Topic', 'accordion_qa_forum' ),
  		'edit_item'             => __( 'Edit Forum Topic', 'accordion_qa_forum' ),
  		'update_item'           => __( 'Update Forum Topic', 'accordion_qa_forum' ),
  		'view_item'             => __( 'View Forum Topic', 'accordion_qa_forum' ),
  		'view_items'            => __( 'View Forum Topics', 'accordion_qa_forum' ),
  		'search_items'          => __( 'Search Forum Topic', 'accordion_qa_forum' ),
  		'not_found'             => __( 'Not found', 'accordion_qa_forum' ),
  		'not_found_in_trash'    => __( 'Not found in Trash', 'accordion_qa_forum' ),
  		'featured_image'        => __( 'Featured Image', 'accordion_qa_forum' ),
  		'set_featured_image'    => __( 'Set featured image', 'accordion_qa_forum' ),
  		'remove_featured_image' => __( 'Remove featured image', 'accordion_qa_forum' ),
  		'use_featured_image'    => __( 'Use as featured image', 'accordion_qa_forum' ),
  		'insert_into_item'      => __( 'Insert into item', 'accordion_qa_forum' ),
  		'uploaded_to_this_item' => __( 'Uploaded to this item', 'accordion_qa_forum' ),
  		'items_list'            => __( 'Forum Topics list', 'accordion_qa_forum' ),
  		'items_list_navigation' => __( 'Forum Topics list navigation', 'accordion_qa_forum' ),
  		'filter_items_list'     => __( 'Filter Forum Topics list', 'accordion_qa_forum' ),
  	);
  	$args = array(
  		'label'                 => __( 'Q/A Forum Topic', 'accordion_qa_forum' ),
  		'description'           => __( 'Q/A Forum Topic', 'accordion_qa_forum' ),
  		'labels'                => $labels,
  		'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes', 'post-formats', ),
  		'taxonomies'            => array( 'category', 'post_tag' ),
  		'hierarchical'          => true,
  		'public'                => true,
  		'show_ui'               => true,
  		'show_in_menu'          => true,
  		'menu_position'         => 20,
  		'menu_icon'             => 'dashicons-calendar-alt',
  		'show_in_admin_bar'     => true,
  		'show_in_nav_menus'     => true,
  		'can_export'            => true,
  		'has_archive'           => true,
  		'exclude_from_search'   => false,
  		'publicly_queryable'    => true,
  		'capability_type'       => 'page',
  		'show_in_rest'          => true,
  	);
  	register_post_type( 'qa_acordion_forum_po', $args );

  }

}
