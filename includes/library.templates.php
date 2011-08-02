<?php
/**
 * This file contains a library of common templates accessed by functions
 *
 * @package PageLines Core
 *
 **/

// ======================================
// = Sidebar Setup & Template Functions =
// ======================================

/**
 * Sidebar - Call & Markup
 *
 */

function pagelines_draw_sidebar($id, $name, $default = null){?>
	<ul id="<?php echo 'list_'.$id; ?>" class="sidebar_widgets fix">
		<?php if (!dynamic_sidebar($name)){ pagelines_default_widget( $id, $name, $default); } ?>
	</ul>
<?php }

/**
 * Sidebar - Default Widget
 *
 */
function pagelines_default_widget($id, $name, $default){
	if(isset($default) && !pagelines('sidebar_no_default')):
	
		get_template_part( $default ); 
		
	elseif(!pagelines('sidebar_no_default')):
	?>	

	<li class="widget-default no_<?php echo $id;?>">
			<h3 class="widget-title">Add Widgets (<?php echo $name;?>)</h3>
			<p class="fix">This is your <?php echo $name;?> but it needs some widgets!<br/> Easy! Just add some content to it in your <a href="<?php echo admin_url('widgets.php');?>">widgets panel</a>.	
			</p>
			<?php echo blink('Add Widgets &rarr;', 'link', 'black', array('action' => admin_url('widgets.php'), 'clear' => true)); ?>
	</li>

<?php endif;
	}

/**
 * Sidebar - Standard Sidebar Setup
 *
 */
function pagelines_standard_sidebar($name, $description){
	return array(
		'name'=> $name,
		'description' => $description,
	    'before_widget' => '<li id="%1$s" class="%2$s widget fix"><div class="widget-pad">',
	    'after_widget' => '</div></li>',
	    'before_title' => '<h3 class="widget-title">',
	    'after_title' => '</h3>'
	);
}


/**
 * Javascript Confirmation
 *
 * @param string $name Function name, to be used in the input
 * @param string $text The text of the confirmation
 */
function pl_action_confirm($name, $text){ 
	?>
	<script language="jscript" type="text/javascript"> function <?php echo $name;?>(){	
			var a = confirm ("<?php echo esc_js( $text );?>");
			if(a) {
				jQuery("#input-full-submit").val(1);
				return true;
			} else return false;
		}
	</script>
<?php }

/**
 * PageLines Search Form
 *
 * @param bool $echo 
 */
function pagelines_search_form( $echo = true ){ 

	$searchfield = sprintf('<input type="text" value="%1$s" name="s" class="searchfield" onfocus="if(this.value == \'%1$s\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'%1$s\';}" />', __('Search', 'pagelines'));
	
	$searchimage = sprintf('<input type="image" class="submit btn" name="submit" src="%s" alt="Go" />', PL_IMAGES.'/search-btn.png');
	
	$searchform = sprintf('<form method="get" class="searchform" onsubmit="this.submit();return false;" action="%s/" ><fieldset>%s %s</fieldset></form>', home_url(), $searchfield, $searchimage);
	
	if ( $echo )
		echo apply_filters('pagelines_search_form', $searchform);
	else
		return apply_filters('pagelines_search_form', $searchform);
}


/**
 * PageLines <head> Includes
 *
 */
function pagelines_head_common(){
	
	pagelines_register_hook('pagelines_code_before_head'); // Hook 

	printf('<meta http-equiv="Content-Type" content="%s; charset=%s" />',  get_bloginfo('html_type'),  get_bloginfo('charset'));

	// Draw Page <title> Tag
	pagelines_title_tag();
	
	// Some Credit
	if(!VDEV)
		echo "<!-- Platform WordPress Framework By PageLines - www.PageLines.com -->\n";
		
	// Meta Images
	if(pagelines_option('pagelines_favicon'))
		printf('<link rel="shortcut icon" href="%s" type="image/x-icon" />%s', pagelines_option('pagelines_favicon'), "\n");
	
	if(pagelines_option('pagelines_touchicon'))
		printf('<link rel="apple-touch-icon" href="%s" />%s', pagelines_option('pagelines_touchicon'), "\n");

	// Meta Data Profiles
	if(!apply_filters( 'pagelines_xfn', '' ))
		echo '<link rel="profile" href="http://gmpg.org/xfn/11" />'."\n";

	
	// bbPress Header... doesn't support hooks, or they need to be reloaded.
//	if( pagelines_bbpress_forum() ){ 			
//		pagelines_load_css( bb_get_stylesheet_uri(), 'pagelines-bbpress', CORE_VERSION);
//		bb_feed_head();
//		bb_head(); 
//	}

	// Get Common CSS & Reset
	pagelines_load_css_relative('css/common.css', 'pagelines-common');

	// Get CSS Layout
	pagelines_load_css_relative('css/layout.css', 'pagelines-layout');
	
	// Get CSS Objects & Grids
	pagelines_load_css_relative('css/objects.css', 'pagelines-objects');

	// Get Pro Styles
	if(VPRO)
		pagelines_load_css_relative('pro/pro.css', 'pagelines-pro');
	
	// Get Main Styles
	
	// Allow for PHP include of Framework CSS
	if(apply_filters( 'pagelines_platform_css', '' ))
		pagelines_load_css(  PARENT_URL.'/style.css', 'pagelines-platform', pagelines_get_style_ver( true ));

	// WordPress CSS Api
	pagelines_load_css(  get_bloginfo('stylesheet_url'), 'pagelines-stylesheet', pagelines_get_style_ver());

	// MediaWiki Integration
	if ( pagelines_mediawiki() )
		pl_mediawiki_head();

	// RTL Language Support
	if(is_rtl()) 
		pagelines_load_css_relative( 'rtl.css', 'pagelines-rtl');
	
	// Queue Common Javascript Libraries
	wp_enqueue_script("jquery"); 
	
	// Fix IE and special handling
	pagelines_fix_ie();
	
	// Cufon replacement 
	pagelines_font_replacement();
	
	// Headerscripts option > custom code
	print_pagelines_option('headerscripts'); // Header Scripts Input Option
}

function pagelines_mediawiki(){
	
	global $pl_mediawiki; 
	
	if ($pl_mediawiki) 
		return true;
	else 
		return false;
}

function pl_mediawiki_head(){

	global $pl_mediawiki_scripts; 

	pagelines_load_css_relative('css/wiki.css', 'pagelines-wiki');

	echo $pl_mediawiki_scripts;

}

function mediawiki_title(){
	global $wgOut; 
	echo $wgOut->mPagetitle;
}

function pagelines_integration_top(){
	
	if(pagelines_is_buddypress_page())
		printf('<div id="%s" class="fix"><div class="content fix">', 'buddypress-page');
	
	elseif(pagelines_mediawiki())
		printf('<div id="%s" class="fix">', 'mediawiki-page');
	
}

function pagelines_integration_bottom(){
	if(pagelines_is_buddypress_page())
		echo "</div></div>";
		
	elseif( pagelines_mediawiki() )
		echo "</div>";
}
	
function pagelines_title_tag(){
	/*
		Title Metatag
	*/
	echo "\n<title>";

	// BuddyPress has its own title.
	if( pagelines_bbpress_forum() )
		bb_title();
	elseif( pagelines_is_buddypress_page() )
		bp_page_title();
	elseif( pagelines_mediawiki() )
		mediawiki_title();
	else {
		if ( !function_exists( 'aiosp_meta' ) && !function_exists( 'wpseo_get_value' ) ) {
		// Pagelines seo titles.
			global $page, $paged;
			$title = wp_title( '|', false, 'right' );

			// Add the blog name.
			$title .= get_bloginfo( 'name' );

			// Add the blog description for the home/front page.
			$title .= ( ( is_home() || is_front_page() ) && get_bloginfo( 'description', 'display' ) ) ? ' | ' . get_bloginfo( 'description', 'display' ) : '';

			// Add a page number if necessary:
			$title .= ( $paged >= 2 || $page >= 2 ) ? ' | ' . sprintf( __( 'Page %s', 'pagelines' ), max( $paged, $page ) ) : '';
		} else
			$title = trim( wp_title( '', false ) );
		
		// Print the title.
		echo apply_filters( 'pagelines_meta_title', $title );
	}
	echo "</title>\n";
}	
	
/**
 * 
 *  Do dynamic CSS, hooked in head; should go last.
 *
 */	
function do_dynamic_css(){
	get_dynamic_css();
}	
	
/**
 * 
 *  Fix IE to the extent possible
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.3.3
 *
 */
function pagelines_fix_ie( ){
	
	if(pagelines('google_ie'))
		echo '<!--[if lt IE 8]> <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE8.js"></script> <![endif]-->'."\n";

	
	echo '<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->'."\n";
	
	/*
		IE File Setting up with conditionals
		TODO Why doesnt WP allow you to conditionally enqueue scripts?
	*/

	// If IE7 add the Internet Explorer 7 specific stylesheet
	global $wp_styles;
	wp_enqueue_style('ie7-style', PL_CSS  . '/ie7.css', array(), CORE_VERSION);
	$wp_styles->add_data( 'ie7-style', 'conditional', 'IE 7' );
	
} 

/**
 * 
 *  Cufon Font Replacement
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.3.3
 *
 */
function pagelines_font_replacement( $default_font = ''){
	
	if(pagelines_option('typekit_script')){
		echo pagelines_option('typekit_script');
	}
	
	if(pagelines_option('fontreplacement')){
		global $cufon_font_path;
		
		if(pagelines_option('font_file')) $cufon_font_path = pagelines_option('font_file');
		elseif($default_font) $cufon_font_path = PL_JS.'/'.$default_font;
		else $cufon_font_path = null;
		
		// ===============================
		// = Hook JS Libraries to Footer =
		// ===============================
		add_action('wp_footer', 'font_replacement_scripts');
		function font_replacement_scripts(){
			
			global $cufon_font_path;

			wp_register_script('cufon', PL_ADMIN_JS.'/type.cufon.js', 'jquery', '1.09i', true);
			wp_print_scripts('cufon');
			
			if(isset($cufon_font_path)){
				wp_register_script('cufon_font', $cufon_font_path, 'cufon');
				wp_print_scripts('cufon_font');
			}
		
		}
		
		add_action('wp_head', 'cufon_inline_script');
		function cufon_inline_script(){
			?><script type="text/javascript"><?php 
			if(pagelines('replace_font')): 
				?>jQuery(document).ready(function () { Cufon.replace('<?php echo pagelines_option("replace_font"); ?>', {hover: true}); });<?php 
			endif;
			?></script><?php
		 }
 	}
}


/**
 * 
 *  Fallback for navigation, if it isn't set up
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */
function pagelines_nav_fallback() {
	global $post; ?>
	
	<ul id="menu-nav" class="main-nav<?php echo pagelines_nav_classes();?>">
		<?php wp_list_pages( 'title_li=&sort_column=menu_order&depth=3'); ?>
	</ul><?php
}

/**
 * 
 *  Returns child pages for subnav, setup in hierarchy
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */
function pagelines_page_subnav(){ 
	global $post; 
	if(!is_404() && isset($post) && is_object($post) && !pagelines_option('hide_sub_header') && ($post->post_parent || wp_list_pages("title_li=&child_of=".$post->ID."&echo=0"))):?>
	<ul class="secondnav_menu lcolor3">
		<?php 
			if(count($post->ancestors)>=2){
				$reverse_ancestors = array_reverse($post->ancestors);
				$children = wp_list_pages("title_li=&depth=1&child_of=".$reverse_ancestors[0]."&echo=0&sort_column=menu_order");	
			}elseif($post->post_parent){ $children = wp_list_pages("title_li=&depth=1&child_of=".$post->post_parent."&echo=0&sort_column=menu_order");
			}else{	$children = wp_list_pages("title_li=&depth=1&child_of=".$post->ID."&echo=0&sort_column=menu_order");}

			if ($children) { echo $children;}
		?>
	</ul>
	<?php endif;
}

/**
 * 
 *  The main site logo template
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */
function pagelines_main_logo(){ 
	if(pagelines_option('pagelines_custom_logo')){
		
		$site_logo = sprintf( '<a class="mainlogo-link" href="%s" title="%s"><img class="mainlogo-img" src="%s" alt="%s" /></a>', home_url(), get_bloginfo('name'), esc_url(pagelines_option('pagelines_custom_logo')), get_bloginfo('name'));
		
		echo apply_filters('pagelines_site_logo', $site_logo);
		
	} else {
		
		$site_title = sprintf( '<div class="title-container"><a class="home site-title" href="%s" title="%s">%s</a><h6 class="site-description subhead">%s</h6></div>', esc_url(home_url()), __('Home','pagelines'), get_bloginfo('name'), get_bloginfo('description'));
		
		echo apply_filters('pagelines_site_title', $site_title);
		
	}
		
}




/**
 * 
 *  Adds PageLines to Admin Bar
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.3.0
 *
 */
function pagelines_settings_menu_link(  ){ 
	global $wp_admin_bar;
	global $pagelines_template;
	if ( !current_user_can('edit_theme_options') )
		return;

	$wp_admin_bar->add_menu( array( 'id' => 'pagelines_settings_adminbar', 'title' => __("PageLines Settings", 'pagelines'), 'href' => admin_url( 'admin.php?page=pagelines' ) ) );

	if(isset($pagelines_template->template_name)){
		$page_type = __("Template: ", 'pagelines') . ucfirst($pagelines_template->template_name);
		$wp_admin_bar->add_menu( array( 'id' => 'template_type', 'title' => $page_type, 'href' => admin_url( 'admin.php?page=pagelines&selectedtab=2' ) ) );
	}
}

/**
 * 
 *  PageLines Attribution
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.3.3
 *
 */
function pagelines_cred(){ 
	
	if(pagelines_option('no_credit') || !VDEV){
		
		
		$img 	= sprintf('<img src="%s" alt="%s by PageLines" />', apply_filters('pagelines_leaf_image', PL_IMAGES.'/pagelines.png'), THEMENAME);
		
		if(get_edit_post_link()){
			$url = get_edit_post_link();
		} else {
			$url = load_pagelines_option('partner_link', 'http://www.pagelines.com/');
		}
		
		$link = (!apply_filters('no_leaf_link', '')) ? sprintf('<a class="plimage" target="_blank" href="%s" title="%s">%s</a>', $url, 'PageLines', $img ) : $img;
		
		$cred = sprintf('<div id="cred" class="pagelines">%s</div><div class="clear"></div>', $link);
	
		echo apply_filters('pagelines_leaf', $cred);
		
	}

}


