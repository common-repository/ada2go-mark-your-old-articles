<?php
// Check if POST
if($_POST AND (is_admin())) {
		// set var from post
		$new_days_content = sanitize_text_field( $_POST['days'] );
		$new_message_content = sanitize_text_field( $_POST['message'] );
		$new_message_content = htmlentities($new_message_content); 
	
global $wpdb;  
$wpdb->update( 
	$wpdb->prefix.'markoldposts_settings',  
	array( 
		'name' => $new_days_content,
		'message' => $new_message_content
	), 
	array( 
		'ID' => 1 
	),
	array( 
		'%d', '%s'	
	), 
	array( '%d' , '%s'	
		 ) 
);
	echo __('<div class="updated">
    <p>'.__('Erledigt! Alles gespeichert!', 'ada2go-mark-old-posts').'</p>
  </div>');
} // END if($_POST)

function a2g_mop_show_mili_sec() {	
	global $wpdb;	
	$get_m_data	=	$wpdb-> get_row("SELECT *  FROM ".$wpdb->prefix."markoldposts_settings WHERE id = 1");	
	return $get_m_data->name;
}

function a2g_mop_show_text() {	
	global $wpdb;	
	$get_m_data	=	$wpdb-> get_row("SELECT *  FROM ".$wpdb->prefix."markoldposts_settings WHERE id = 1");	
	return $get_m_data->message;
}
?>
<h1><?php echo esc_html_e('Mark Older Posts - Einstellungen', 'ada2go-mark-old-posts'); ?></h1>
<form action="?page=a2g_mop_settings" method="post">
<h2><?php echo esc_html_e('Wie viele Sekunden sollen eingestellt werden?', 'ada2go-mark-old-posts'); ?></h2>
	<p><?php echo esc_html_e('Trage hier die Sekundenzahl ein, die genutzt werden soll. Verzichte auf Komma, Leerzeichen oder Punkt.', 'ada2go-mark-old-posts'); ?>
		<br><?php echo esc_html_e('Beispiel:', 'ada2go-mark-old-posts'); ?><br>
		<ul>
		<li><?php echo esc_html_e('2678400 f&uuml;r 31 Tage', 'ada2go-mark-old-posts'); ?></li>
		<li><?php echo esc_html_e('7776000 f&uuml;r 3 Monate', 'ada2go-mark-old-posts'); ?></li>
		<li><?php echo esc_html_e('15552000 f&uuml;r 6 Monate', 'ada2go-mark-old-posts'); ?></li>
	</ul></p>
	<p><input type="text" name="days" value="<?php echo esc_attr(a2g_mop_show_mili_sec()); ?>"></p>
<hr>
<h2><?php echo esc_html_e('Nachricht', 'ada2go-mark-old-posts'); ?></h2>
	<p><?php echo esc_html_e('Trage hier die Nachricht ein, die angezeigt werden soll.', 'ada2go-mark-old-posts'); ?>
		<?php echo esc_html_e('Falls gew&uuml;nscht, kannst du den Platzhalter %sec% verwenden. Dieser wird durch die Tage ersetzt. Beispiel "Dieser Beitrag ist schon %sec% Tage alt."', 'ada2go-mark-old-posts'); ?>
		<br>
		<textarea name="message" style="width:300px;height:300px;"><?php echo a2g_mop_show_text(); ?></textarea>
	</p>
	<input type="submit" value="<?php echo esc_html_e('&Auml;nderungen speichern', 'ada2go-mark-old-posts'); ?>" class="button">
</form>
<hr>
<h2><?php echo esc_html_e('Passe dein CSS an', 'ada2go-mark-old-posts'); ?></h2>
	<p>
	<?php echo esc_html_e('Um dein Design anzupassen, kannst du folgende zwei Klassen verwenden:', 'ada2go-mark-old-posts'); ?>:<br>
		<b>.older-post-text</b> <?php echo esc_html_e('Diese Klasse ist f&uuml;r den Text in der angezeigten Box verantwortlich.', 'ada2go-mark-old-posts'); ?><br>
		<b>.older-post-hint</b> <?php echo esc_html_e('Diese Klasse ist f&uuml;r das Design und die Position der Box verantwortlich', 'ada2go-mark-old-posts'); ?>.
    <br><br><a href="<?php echo esc_url( get_site_url().'/wp-admin/customize.php' ); ?>" target="_blank">
  <input type="submit" value="<?php esc_html_e( '&Ouml;ffne den Customizer in einem neuen Fenster', 'ada2go-mark-old-posts' ); ?>" class="button"></a>
    
    
</p>
<hr>
<h2><?php echo esc_html_e('Liste aller markierten Beitr&auml;ge', 'ada2go-mark-old-posts'); ?></h2>
	<p>
	<?php echo esc_html_e('Damit du nicht lange rumsuchen musst, hier eine Liste aller Beitr&auml;ge, die durch das Plugin markiert wurden:', 'ada2go-mark-old-posts'); ?>
  <br><br>
  <?php
    $now = time();
    $your_date = time()-a2g_mop_show_mili_sec();
    $datediff = $now - $your_date;
    $a2g_the_days = round($datediff / (60 * 60 * 24));
    $a2g_the_days = $a2g_the_days." days ago";


$args = array(
    'date_query' => array(
        array(
            'column' => 'post_modified_gmt',
            'before'  => $a2g_the_days,
        ),
    ),
    'posts_per_page' => -1,
);
    $posts = get_posts($args);
 foreach ( $posts as $posts_out ) {
        echo  '<a href="' . get_permalink( $posts_out->ID ) . '" target="_blank">'.esc_html('Vorschau', 'ada2go-mark-old-posts').'</a> |'.esc_html('Bearbeiten', 'ada2go-mark-old-posts').': <a href="post.php?post=' . $posts_out->ID . '&action=edit" target="_blank">' . $posts_out->post_title . '</a><br>' ;
      }
  ?>
</p>