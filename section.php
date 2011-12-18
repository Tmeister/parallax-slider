<?php
/*
	Section: Parallax Slider
	Author: Enrique Chavez
	Author URI: http://www.klr20mg.com
	Version: 0.1.2
	Description: Use the Parallax slider to add a new feature slider look to your website. Slides display full size images and use thumbnails to help users find the information they want.
	Class Name: TmParallaxSlider
	Cloning: false
	Demo: http://pagelines.tmeister.net/parallax-slider/
 	External: http://pagelines.tmeister.net/parallax-slider/
 	Long: Use the Parallax slider to add a new feature slider look to your website. Slides display full size images and use thumbnails to help users find the information they want.


 	Changelog:	

 	Version 0.1.2
 		- Bug Fix, $oset for internal pages settings. 

 	Version 0.1.1
 		- Bug Fix, Show images smaller than 900x350   

 	Version 0.1 
 		Public Release
 	




*/

class TmParallaxSlider extends PageLinesSection {

	var $taxID = "parallax-sets";
	var $ptID  = 'tm_parallax';

	function section_persistent(){
		$this->post_type_setup();
		$this->post_meta_setup();
		add_image_size('parallax_slider', 900, 350, true);
		add_image_size('parallax_thumb', 80, 55, true);
	}

	function section_head($clone_id =  null){

		global $pagelines_ID;

		$oset  = array('post_id' => $pagelines_ID, 'clone_id' => $clone_id);
		
		$auto  = ( ploption('tm_parallaxtimeout', $oset) ) ? ploption('tm_parallaxtimeout', $oset) : '7000';
		$speed = ( ploption('tm_parallaxfspeed', $oset) ) ? ploption('tm_parallaxfspeed', $oset) : '1000';
	?>
		<style type="text/css" media="screen">
			#<?=$this->id?> .content{
				width:100% !important;
				min-width:100% !important;
			}
			#<?=$this->id?> .content-pad{
				padding:0px;
			}
			.pxs_container,
			.pxs_bg div,
			ul.pxs_slider,
			ul.pxs_slider li{
				height: 470px !important;
			}
			#pxs_container{
				background: <?=ploption('tm_parallax_background', $oset)?> !important;
			}
			.pxs_bg .pxs_bg1{
				background:url(<?=ploption('tm_parallax_background_one', $oset)?>) !important;
			}
			.pxs_bg .pxs_bg2{
				background:url(<?=ploption('tm_parallax_background_two', $oset)?>) !important;
			}
		</style>
		<script>
			jQuery(function($) {
				var $pxs_container	= $('#pxs_container');
				$pxs_container.parallaxSlider({
					auto            : <?=$auto?>,
					thumbRotation   : false,
					speed			: <?=$speed?>
				});
			});
        </script>
	<?
	}

	function section_scripts() {
		return array(
			'jquery.easing' => array(
				'file'       => $this->base_url . '/jquery.easing.1.3.js',
				'dependancy' => array('jquery'),
				'location'   => 'footer',
				'version'    => '1.3'
			),
			'paralax' => array(
					'file'       => $this->base_url . '/parallax.js',
					'dependancy' => array('jquery.easing'),
					'location'   => 'footer',
					'version'    => '1.0'
				)

		);
	}

	function section_template( $clone_id = null ) {
		global $post;
		global $pagelines_ID;
		
		$oset = array('post_id' => $pagelines_ID, 'clone_id' => $clone_id);

		$limit = ( ploption('tm_parallax_items', $oset) ) ? ploption('tm_parallax_items', $oset) : '5';
		$set = ploption('tm_parallax_set', $oset);
		$sliders = $this->get_parallax_sliders($set, $limit);

		if( !count($sliders) ){
			echo setup_section_notify($this, __('Sorry,there is no sliders to display.', $this->ptID) );
			return;
		}
		$current_page_post = $post;
		/**********************************************************************
		* We have slider, but check for images
		***********************************************************************/
		$found = false;
		foreach ($sliders as $post){
			setup_postdata($post); 
			$oset_in = array('post_id' => $post->ID);
			$image = $this->get_image( $post->ID, 'parallax_slider', plmeta('parallax_image', $oset_in) );
			if( strlen($image) && $image != -2 ){
				$found = true;
			}
		}
		if( ! $found ){
			echo setup_section_notify($this, __('There is Parallax Sliders, but none had images.<br>Please, verify that the images you used are bigger than 900px X 350px.', $this->ptID), get_admin_url().'edit.php?post_type=tm_parallax', 'Please upload images');
			return;
		}
		/**********************************************************************
		* At least one slider have a image. go..
		**********************************************************************/

	?>
		<div id="pxs_container" class="pxs_container">
			<div class="pxs_bg">
				<div class="pxs_bg1"></div>
				<div class="pxs_bg2"></div>
			</div>
			<div class="pxs_loading"><?= ( $sliders ) ? __('Loading images...', $this->ptID) : __('Whoops, No images', $this->ptID)?> </div>
			<div class="pxs_slider_wrapper">
				<ul class="pxs_slider">
					<?php 
						foreach ($sliders as $post):
							setup_postdata($post); 
							$oset_in = array('post_id' => $post->ID);
							$image = $this->get_image( $post->ID, 'parallax_slider', plmeta('parallax_image', $oset_in) );
					?>
						<li>
							<a href="<?=plmeta('parallax-link-url', $oset)?>">
								<img src="<?=$image?>" alt="<?the_title()?>">
							</a>
						</li>
					<?php endforeach ?>
				</ul>
				<div class="pxs_navigation">
					<span class="pxs_next"></span>
					<span class="pxs_prev"></span>
				</div>
				<ul class="pxs_thumbnails">
					<?php 
						foreach ($sliders as $post): 
							setup_postdata($post); 
							$oset_in = array('post_id' => $post->ID);
							$image = $this->get_image( $post->ID, 'parallax_thumb', plmeta('parallax_image', $oset_in) );
						?>
						<li>
							<img src="<?=$image?>" title="<?the_title()?>">
						</li>
					<?php endforeach; $post = $current_page_post; ?>
				</ul>
			</div>
		</div>
	<?
	}

	function get_image ($postID, $size = 'thumbnail', $url = null){
		$args = array(
			'numberposts' => 1,
			'order'=> 'ASC',
			'post_mime_type' => 'image',
			'post_parent' => $postID,
			'post_status' => null,
			'post_type' => 'attachment'
		);
		
		$attachments = get_children( $args );
		if ($attachments) {
			foreach($attachments as $attachment) {
				$image_attributes = wp_get_attachment_image_src( $attachment->ID, $size )  ? wp_get_attachment_image_src( $attachment->ID, $size ) : wp_get_attachment_image_src( $attachment->ID, 'full' );
				return wp_get_attachment_thumb_url( $attachment->ID );
			}
		}else{
			/***
			* NO IMAGE ATTACHED MAYBE A PAGELINES BUG // CHECK LATER
			**/
			switch( $size ){
				case 'parallax_slider':
					$full = preg_replace('/(\.gif|\.jpg|\.png)/', '-900x350$1', $url); 
					if( is_array( getimagesize($full) )){
						return preg_replace('/(\.gif|\.jpg|\.png)/', '-900x350$1', $url);
					}else{
						return $url;
					}
					break; 
				case 'parallax_thumb':
					return preg_replace('/(\.gif|\.jpg|\.png)/', '-80x55$1', $url);
					break;
				default:
					return false;
			}
			
		}
	}

	function get_parallax_sliders( $set = null, $limit = null){
		$query = array();
		$query['orderby'] 	= 'ID';
		$query['post_type'] = $this->ptID;
		$query[ $this->taxID ] = $set;
		if(isset($limit)){
			$query['showposts'] = $limit;
		}
		$q = new WP_Query($query);

		if(is_array($q->posts))
			return $q->posts;
		else
			return array();
	}

	function section_optionator( $settings ){
		$settings = wp_parse_args($settings, $this->optionator_default);

		$page_metatab_array = array(

			'tm_parallax_set' 	=> array(
				'version' 		=> 'pro',
				'default'		=> 'default-parallax',
				'type' 			=> 'select_taxonomy',
				'taxonomy_id'	=> $this->taxID,
				'title' 		=> 'Select parallax Set To Show',
				'shortexp'		=> 'The "set" or category of feature posts',
				'inputlabel'	=> 'Select parallax Set',
				'exp' 			=> 'If you are using the Parallax section, select the Parallax set you would like to show on this page. if don\'t select a set the slider, will show all Parallax Slider entries'
			),

			'tm_parallax_items' => array(
				'version' 		=> 'pro',
				'default'		=> 5,
				'type' 			=> 'text_small',
				'inputlabel'	=> 'Number of sliders to show',
				'title' 		=> 'Number of Slides',
				'shortexp'		=> 'The amount of slides to show on this page, Default value is 5'
			),

			'tm_parallax_background' => array(
				'default' 		=> '#ffffff',
				'version'		=> 'pro',
				'type' 			=> 'colorpicker',
				'inputlabel' 	=> 'First Layer Background Color',
				'title' 		=> 'Parallax container background color',
				'shortexp' 		=> 'Select the background color for parallax container ',
				'exp' 			=> 'The Parallax backgound is created with three layers to create an seudo3D effect, the first layer is the background color. Default value is #ffffff ( White )'
			),

			'tm_parallax_background_one' => array(
				'version'		=> 'pro',
				'type' 			=> 'image_upload',
				'inputlabel' 	=> 'Background image',
				'title' 		=> 'Second Layer Image',
				'shortexp' 		=> 'Select the background image to use in the second layer',
				'exp' 			=> 'The Parallax backgound is created with three layers to create an seudo3D effect, the second layer is pattern image.'
			),

			'tm_parallax_background_two' => array(
				'version'		=> 'pro',
				'type' 			=> 'image_upload',
				'inputlabel' 	=> 'Background image',
				'title' 		=> 'Third Layer Image',
				'shortexp' 		=> 'Select the background image to use in the third layer',
				'exp' 			=> 'The Parallax backgound is created with three layers to create an seudo3D effect, the third layer is pattern image.'
			),

			'tm_parallaxtimeout' => array(
				'default' 		=> '7000',
				'version'		=> 'pro',
				'type' 			=> 'text_small',
				'inputlabel' 	=> 'Timeout (ms)',
				'title' 		=> 'Parallax Viewing Time (Timeout)',
				'shortexp' 		=> 'The amount of time a feature is set before it transitions in milliseconds',
				'exp' 			=> 'Set this to 0 to only transition on manual navigation. Use milliseconds, for example 10000 equals 10 seconds of timeout. Default value is 7000 ms ( 7s )'
			),
			
			'tm_parallaxfspeed' => array(
				'default' 		=> 1000,
				'version'		=> 'pro',
				'type' 			=> 'text_small',
				'inputlabel' 	=> 'Transition Speed (ms)',
				'title' 		=> 'Parallax Transition Time (Timeout)',
				'shortexp' 		=> 'The time it takes for your features to transition in milliseconds',
				'exp' 			=> 'Use milliseconds, for example 1500 equals 1.5 seconds of transition time. Default value is 1000 ms ( 1s )'
			)
		);

		$metatab_settings = array(
				'id' 		=> 'tm_parallax_meta',
				'name' 		=> "Parallax Slider",
				'icon' 		=> $this->icon,
				'clone_id'	=> $settings['clone_id'],
				'active'	=> $settings['active']
			);

		register_metatab($metatab_settings, $page_metatab_array);

	}

	function post_type_setup(){
		$args = array(
			'label' 			=> __('Parallax Slider', $this->ptID),
			'singular_label' 	=> __('Slider', $this->ptID),
			'description' 		=> __('For setting slides on the parallax page template', $this->ptID),
			'taxonomies'		=> array( $this->taxID ),
			'menu_icon'			=> $this->icon,
			'supports'			=> 'title'
		);
		$taxonomies = array(
			$this->taxID => array(
				"label"          => __('Parallax Sets', $this->ptID),
				"singular_label" => __('Parallax Set', $this->ptID),
			)
		);
		$columns = array(
			"cb" 					=> "<input type=\"checkbox\" />",
			"title" 				=> "Title",
			"parallax_image" 		=> "Media",
			$this->taxID			=> "Parallax Slider Sets"
		);
		$this->post_type = new PageLinesPostType( $this->ptID, $args, $taxonomies, $columns, array(&$this, 'column_display') );
	}

	function post_meta_setup(){
		$pt_tab_options = array(
			'parallax_image' => array(
				'shortexp' => 'Upload the slide image.',
				'title'    => 'Parallax image slider',
				'type'     => 'image_upload'
			),

			'parallax-link-url' => array(
				'shortexp' 			=> 'Adding a URL here will add a link to your parallax slide',
				'title' 			=> 'Parallax Slider Link URL',
				'label'				=> 'Enter arallax Slider Link URL',
				'type' 				=> 'text',
				'exp'				=> 'Sets the url of the link of the slider.'
			)
		);

		$pt_panel = array(
				'id' 		=> 'parallax-metapanel',
				'name' 		=> "Parallax Slider Setup Options",
				'posttype' 	=> array( $this->ptID ),
				'hide_tabs'	=> true
			);

		$pt_panel =  new PageLinesMetaPanel( $pt_panel );


		$pt_tab = array(
			'id' 		=> 'parallax-type-metatab',
			'name' 		=> "Parallax Slider Setup Options",
			'icon' 		=> $this->icon,
		);

		$pt_panel->register_tab( $pt_tab, $pt_tab_options );
	}

	function column_display($column){
		global $post;
		switch ($column){
			case "parallax_image":
				echo '<img src="'.m_pagelines('parallax_image', $post->ID).'" style="max-width: 300px; max-height: 100px" />';
			break;
			case $this->taxID:
				echo get_the_term_list($post->ID, $this->taxID, '', ', ','');
			break;
		}
	}

}
