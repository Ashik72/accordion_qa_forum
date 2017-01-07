<?php

// d(TheForum::get_cat_title(32));
// d(TheForum::get_category_posts(32));
// d(TheForum::get_categories());


$forum_cats = TheForum::get_categories();

 ?>

<div id="accordion_qa_forum_cats">

<?php

  foreach ($forum_cats as $key => $category) {
    ?>

    <h3><?php _e(TheForum::get_cat_title($category->term_id)) ?></h3>
    <div>
      <p><?php _e($category->description) ?></p>

      <div data-cat_id="<?php _e($category->term_id); ?>" class="cat_posts_div" data-post_ids="<?php

      $post_ids = TheForum::get_category_posts($category->term_id);

      $post_ids = (is_array($post_ids) ? $post_ids : array());

      $html = "";
      foreach ($post_ids as $key => $post_id) {

        $html .= $post_id->object_id;

        if (count($post_ids) != ( ((int)$key) + 1 )   )
          $html .= ",";
      }

      _e($html);
       ?>"></div>

    </div>

    <?php
  }

 ?>
</div>


<div id="accordion_qa_forum">
</div>
