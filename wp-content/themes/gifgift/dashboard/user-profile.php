<?php
/**
 * Template Name: User Profile
 *
 * Allow users to update their profiles from Frontend.
 *
 
/* Get user info. */
global $current_user, $wp_roles;
wp_get_current_user();

/* Load the registration file. */
$error = array();    
/* If profile was saved, update profile. */
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

    /* Update user password. */
    if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
        if ( $_POST['pass1'] == $_POST['pass2'] )
            wp_update_user( array( 'ID' => $current_user->ID, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
        else
            $error[] = __('The passwords you entered do not match.  Your password was not updated.', 'profile');
    }

    /* Update user information. */
    if ( !empty( $_POST['url'] ) )
       wp_update_user( array ('ID' => $current_user->ID, 'user_url' => esc_attr( $_POST['url'] )));
    if ( !empty( $_POST['email'] ) ){
        if (!is_email(esc_attr( $_POST['email'] )))
            $error[] = __('The Email you entered is not valid.  please try again.', 'profile');
        elseif(email_exists(esc_attr( $_POST['email'] )) != $current_user->id )
            $error[] = __('This email is already used by another user.  try a different one.', 'profile');
        else{
            wp_update_user( array ('ID' => $current_user->ID, 'user_email' => esc_attr( $_POST['email'] )));
        }
    }

    if ( !empty( $_POST['first-name'] ) )
        update_user_meta( $current_user->ID, 'first_name', esc_attr( $_POST['first-name'] ) );
    if ( !empty( $_POST['last-name'] ) )
        update_user_meta($current_user->ID, 'last_name', esc_attr( $_POST['last-name'] ) );
    if ( count($error) == 0 ) {
        do_action('edit_user_profile_update', $current_user->ID);
        wp_redirect( get_permalink().'?updated=true' ); exit;
    }       
}



get_header();?>
<div class="main-inner-page">
	<div class="container">
		<div class="inner-pages-inner">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>">
        <div class="entry-content entry diff">
            <?php the_content(); ?>
            <?php if ( !is_user_logged_in() ) : ?>

                    <p class="warning">
                        <?php _e('You must be logged in to edit your profile.', 'profile'); ?>
                    </p><!-- .warning -->
            <?php else : ?>
             <h1><span>Update Information for &quot;<?php echo $current_user->user_login ?>&quot;</h1></span></br>
                <?php if ( $_GET['updated'] == 'true' ) : ?> <div id="message" class="updated"><p>Your profile has been updated.</p></div> <?php endif; ?>
                <?php if ( count($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
                <form method="post" id="adduser" action="<?php the_permalink(); ?>">
                    <p class="form-username">
                        <label for="first-name"><?php _e('First Name', 'profile'); ?></label>
                        <input class="text-input" name="first-name" type="text" id="first-name" value="<?php the_author_meta( 'first_name', $current_user->ID ); ?>" />
                    </p><!-- .form-username -->
                    <p class="form-username">
                        <label for="last-name"><?php _e('Last Name', 'profile'); ?></label>
                        <input class="text-input" name="last-name" type="text" id="last-name" value="<?php the_author_meta( 'last_name', $current_user->ID ); ?>" />
                    </p><!-- .form-username -->
                    <p class="form-email">
                        <label for="email"><?php _e('E-mail *', 'profile'); ?></label>
                        <input class="text-input" name="email" type="text" id="email" value="<?php the_author_meta( 'user_email', $current_user->ID ); ?>" />
                    </p><!-- .form-email -->
                    <p class="form-password">
                        <label for="pass1"><?php _e('Password *', 'profile'); ?> </label>
                        <input class="text-input" name="pass1" type="password" id="pass1" />
                    </p><!-- .form-password -->
                    <p class="form-password">
                        <label for="pass2"><?php _e('Repeat Password *', 'profile'); ?></label>
                        <input class="text-input" name="pass2" type="password" id="pass2" />
                    </p><!-- .form-password -->
                    <p class="form-submit">
                        <?php echo $referer; ?>
                        <input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'profile'); ?>" />
                        <?php wp_nonce_field( 'update-user_'. $current_user->ID ) ?>
                        <input name="action" type="hidden" id="action" value="update-user" />
                    </p><!-- .form-submit -->
                </form><!-- #adduser -->
            <?php endif; ?>
        </div><!-- .entry-content -->
    </div><!-- .hentry .post -->
    <?php endwhile; ?>
<?php else: ?>
    <p class="no-data">
        <?php _e('Sorry, no page matched your criteria.', 'profile'); ?>
    </p><!-- .no-data -->
<?php endif; ?>
	</div>
	</div>
	</div>

<?php get_footer();?>