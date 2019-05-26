<?php 
/*
Template Name: Video List
*/
get_header(); ?>

  <section>
    <div class="container">
      <div class="row">
        <div class="col-sm-8">
        
          <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article <?php post_class();?>>
              <h2 class="title"><?php the_title(); ?></h2>
              <?php the_content(); ?>
            </article>

        	<?php if (comments_open()){ ?>
              <?php comments_template('', true); ?>
            <?php } ?>

          <?php endwhile; else: ?>
            <p><?php _e('There is no page found here.','blackboard'); ?></p>
            <p><?php _e('We apologize for any inconvenience, please go back on your browser.','blackboard'); ?></p>
          <?php endif; ?>
          
          <div id="macroVideos">
             <?php remove_filter( 'posts_where', array( 'Groups_Post_Access', 'posts_where' ) ); ?>
             <?php
                $post_type = 'macro-watch';
				$tax = 'macro-type';
				$term = get_queried_object();
				// print the parent heading
			 ?>
			 <h4 class="gov-parent-term"><?php echo $term->name; ?></h4>
			 <?php
				// get all its children
				$child_terms = ""; // first ensure this var is empty
				$child_terms = get_terms ( $tax, array('order' => 'ASC', 'include' => get_term_children ( $term->term_id, $tax )) );
				
				// if any, foreach child term, query the posts
				if ( !empty($child_terms) ){
				foreach ($child_terms as $child_term){
				$child_args="";
				$child_args = array(
				'post_type' => $post_type,
				'tax_query' => array(
				array(
				'taxonomy' => $tax,
				'field' => 'slug',
				'terms' => $child_term->slug,
				'include_children' => false,
				'operator' => 'IN'
				)
				),
				'post_status' => 'publish',
				'order' => 'ASC',
				);
				// query the posts
				$child_query = null;
				$child_query = new WP_Query($child_args);
				if( $child_query->have_posts() ) : ?>
				<h3><?php echo $child_term->name; ?></h3>
				<ul class="vidList">
				<?php while ( $child_query->have_posts() ) : $child_query->the_post(); ?>
				  <li>
					<?php if (is_user_logged_in()){ ?>
                    <?php
                    $user = wp_get_current_user();  
                    $user = new Groups_User( $user->ID );
                  
                    $group0 = Groups_Group::read_by_name( 'Course0_All' ); 
                    $group1 = Groups_Group::read_by_name( 'Course1_Capitalism' );
                    $group2 = Groups_Group::read_by_name( 'Course2_Economy' );
                    $groupM = Groups_Group::read_by_name( 'Subscriber_Macro' ); 
                   
                    if( Groups_User_Group::read( $user->ID, $group1->group_id) || Groups_User_Group::read( $user->ID, $group2->group_id ) || Groups_User_Group::read( $user->ID, $group0->group_id)){
                    ?>
                      <a href="<?php bloginfo('wpurl'); ?>/this-video-is-only-available-to-macro-watch-subscribers/"  class="vidTitleShort"><?php the_title(); ?></a>
                    <?php } elseif( Groups_User_Group::read( $user->ID, $groupM->group_id) ){ ?>
                      <a href="<?php the_permalink(); ?>"  class="vidTitleShort"><?php the_title(); ?></a> 
                    <?php } ?>
                  <?php } else { ?>
                    <a href="<?php bloginfo('wpurl'); ?>/this-video-is-only-available-to-macro-watch-subscribers/"  class="vidTitleShort"><?php the_title(); ?></a>
                  <?php } ?>
				</li>
				<?php endwhile; // end of loop ?>
				</ul>
				<?php endif; // if have_posts()
				
				wp_reset_query();
				} // end foreach #child_terms
				}
             ?> 
             <?php add_filter( 'posts_where', array( 'Groups_Post_Access', 'posts_where' ), 10, 2 ); ?>
          </div><!--#macroVideos-->


        </div>

        <?php get_sidebar('main-right'); ?>

      </div>
    </div>
  </section>

<?php get_footer(); ?>
