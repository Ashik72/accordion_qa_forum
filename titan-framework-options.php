<?php

if (!defined('ABSPATH'))
  exit;


add_action( 'tf_create_options', 's2_sensei_custom_options', 150 );

function s2_sensei_custom_options() {


	$titan = TitanFramework::getInstance( 's2_sensei_custom-options' );
	$section = $titan->createAdminPanel( array(
		    'name' => __( 's2Member Sensei Integration', 's2_sensei_custom' ),
		    'icon'	=> 'dashicons-feedback'
		) );

	$tab = $section->createTab( array(
    		'name' =>  __( 'General Options', 's2_sensei_custom' )
		) );

    $tab->createOption([
			'name' => 'Define s2Member level and course ID (level|course_id)',
			'id' => 's2_sensei_custom_level_cid',
			'type' => 'textarea',
			'desc' => 'level|course_id (ex - 2|309)',
      'default' => ''
			]);

  $tab->createOption( array(
    'name' => 'Unique Key',
    'id' => 's2_sensei_custom_unique_key',
    'type' => 'text',
    'default' => '34df32',
    'desc' => 'Your Unique Key'
    ) );

$uiq_s2_custom = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags(sanitize_text_field($titan->getOption('s2_sensei_custom_unique_key'))))))));

$uiq_s2_custom = preg_replace('/\s+/', '', $uiq_s2_custom);


          $tab->createOption(array(
            'name' => '**<b>s2Member Registration Notifications Link:</b>',
            'type' => 'custom',
            'custom' => '<div style="width: 100%">
<code>'.get_site_url().'/?s2_sensei_custom=1&uiq='.$uiq_s2_custom.'&uid=%%user_id%%</code><br><br>

Put the link above to <strong>s2Member (Pro) > API / Notifications > Registration Notifications</strong>.
<br>

       </div>'

          ));

		$section->createOption( array(
  			  'type' => 'save',
		) );


		/////////////New

/*		$embroidery_sub = $section->createAdminPanel(array('name' => 'Embroidering Pricing'));


		$embroidery_tab = $embroidery_sub->createTab( array(
    		'name' => 'Profiles'
		) );


		$wp_expert_custom_options['embroidery_tab'] = $embroidery_tab;

		return $wp_expert_custom_options;
*/
}


 ?>
