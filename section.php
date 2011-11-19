<?php
/*
	Section: Parallax Slider for Features
	Author: Enrique Chavez
	Author URI: http://www.klr20mg.com
	Version: 0.2
	Description: Awesome slider for your featured post
	Class Name: TmParallaxSlider
	Cloning: true
	Demo: http://dev.tmeister.net
 	External: http://dev.tmeister.net
 	Long: Parallax Slider bla bla marketing stuff...
*/

class TmParallaxSlider extends PageLinesSection {

	var $taxID = "parallax-sets";
	var $ptID  = 'tm_parallax';

	function section_persistent(){
		$this->post_type_setup();
		$this->post_meta_setup();
	}

	function section_head(){
		$stage_height = ploption('tm_parallax_stage_height');
		$auto = ploption('tm_parallaxtimeout');
        $speed = ploption('tm_parallaxfspeed');
	?>
		<style type="text/css" media="all">
			.some{background:#dddddd;}
			.pxs_container,
			.pxs_bg div,
			ul.pxs_slider,
			ul.pxs_slider li{
				height: <?=$stage_height?>px !important;
			}
			#pxs_container{
				background: <?=ploption('tm_parallax_background')?> !important;
			}
			.pxs_bg .pxs_bg1{
				background:url(<?=ploption('tm_parallax_background_one')?>) !important;
			}
			.pxs_bg .pxs_bg2{
				background:url(<?=ploption('tm_parallax_background_two')?>) !important;
			}
		</style>
		<script>
			jQuery(function($) {
				console.log('WTH')
				var $pxs_container	= $('#pxs_container');
				$pxs_container.parallaxSlider({
					auto            : <?=$auto?>,
					thumbRotation   : true,
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
		$limit = ploption('tm_parallax_items');
		$set = ploption('tm_parallax_set');
		$sliders = $this->get_parallax_sliders($set, $limit);

	?>
		<div id="pxs_container" class="pxs_container">
			<div class="pxs_bg">
				<div class="pxs_bg1"></div>
				<div class="pxs_bg2"></div>
			</div>
			<div class="pxs_loading"><?= ( $sliders ) ? __('Loading images...', $this->ptID) : __('Whoops, No images', $this->ptID)?> </div>
			<div class="pxs_slider_wrapper">
				<ul class="pxs_slider">
					<?php foreach ($sliders as $post): setup_postdata($post); $oset = array('post_id' => $post->ID);?>
						<li><a href="<?=plmeta('parallax-link-url', $oset)?>"><img src="<?=plmeta('parallax_image', $oset)?>" alt=""></a></li>
					<?php endforeach ?>
				</ul>
				<div class="pxs_navigation">
					<span class="pxs_next"></span>
					<span class="pxs_prev"></span>
				</div>
				<ul class="pxs_thumbnails">
					<?php foreach ($sliders as $post): setup_postdata($post); $oset = array('post_id' => $post->ID);?>
						<li><img src="<?=plmeta('parallax_thumb', $oset)?>" title="<?the_title()?>"></li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>
	<?
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
				'tm_parallax_stage_height' => array(
					'default' 		=> '470',
					'version'		=> 'pro',
					'type' 			=> 'text_small',
					'inputlabel' 	=> 'Enter the height (In Pixels) of the Parallax Stage Area',
					'title' 		=> 'Parallax Area Height',
					'shortexp' 		=> "Use this feature to change the height of your Parallax area",
					'exp' 			=> "To change the height of your Parallax area, just enter a number in pixels here.",
				),
				'tm_parallax_items' => array(
					'version' 		=> 'pro',
					'default'		=> 5,
					'type' 			=> 'text_small',
					'inputlabel'	=> 'Number of sliders to show',
					'title' 		=> 'Number of Slides',
					'shortexp'		=> 'The amount of slides to show on this page',
					'exp' 			=> 'Enter the max number of slides to show on this page.'
				),
				'tm_parallax_set' 	=> array(
					'version' 		=> 'pro',
					'default'		=> 'default-parallax',
					'type' 			=> 'select_taxonomy',
					'taxonomy_id'	=> $this->taxID,
					'title' 		=> 'Select parallax Set To Show',
					'shortexp'		=> 'The "set" or category of feature posts',
					'inputlabel'	=> 'Select parallax Set',
					'exp' 			=> 'If you are using the feature section, select the feature set you would like to show on this page.'
				),
				'tm_parallaxtimeout' => array(
					'default' 		=> '7000',
					'version'		=> 'pro',
					'type' 			=> 'text_small',
					'inputlabel' 	=> 'Timeout (ms)',
					'title' 		=> 'Parallax Viewing Time (Timeout)',
					'shortexp' 		=> 'The amount of time a feature is set before it transitions in milliseconds',
					'exp' 			=> 'Set this to 0 to only transition on manual navigation. Use milliseconds, for example 10000 equals 10 seconds of timeout.'
				),
				'tm_parallaxfspeed' => array(
					'default' 		=> 1000,
					'version'		=> 'pro',
					'type' 			=> 'text_small',
					'inputlabel' 	=> 'Transition Speed (ms)',
					'title' 		=> 'Parallax Transition Time (Timeout)',
					'shortexp' 		=> 'The time it takes for your features to transition in milliseconds',
					'exp' 			=> 'Use milliseconds, for example 1500 equals 1.5 seconds of transition time.'
				),
				'tm_parallax_background' => array(
					'default' 		=> '#ffffff',
					'version'		=> 'pro',
					'type' 			=> 'colorpicker',
					'inputlabel' 	=> 'First Layer Background Color',
					'title' 		=> 'Parallax container background color',
					'shortexp' 		=> 'Select the background color for parallax container ',
					'exp' 			=> 'The Parallax backgound is created with three layers to create an seudo3D effect, the first layer is the background color.'
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

				'parallax_thumb' => array(
					'shortexp' => 'Add thumbnails to your post for use in thumb navigation. Create an image 85px wide by 35px tall and upload here.',
					'title'    => 'Parallax image Thumb (50px by 30px)',
					'label'    => 'Upload Parallax Thumbnail',
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
