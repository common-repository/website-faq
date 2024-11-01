<?php
/*
Plugin Name: Website FAQ Widget
Plugin URI: http://www.quizpandit.com/
Description: Widget to display website FAQ form on website sidebar
Author: Anand Raju
Version: 1
Author URI: http://www.quizpandit.com/
*/
 
 
class WebsiteFaqWidget extends WP_Widget
{
  function WebsiteFaqWidget()
  {
    $widget_ops = array('classname' => 'WebsiteFaqWidget', 'description' => 'Displays a random post with thumbnail' );
    $this->WP_Widget('WebsiteFaqWidget', 'Website FAQ', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '','category' =>'' ) );
    $title = $instance['title'];
	$cat = $instance['category'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
   <p><label for="<?php echo $this->get_field_id('category'); ?>">Category: 
   <?PHP
  echo '<select name="'.$this->get_field_name('category').'" class="widefat" >';
  echo '<option value="0">All</option>';
	
	global $wpdb;
	$category_table = $wpdb->prefix . "faq_category";
	$sql = "SELECT * FROM $category_table ORDER BY faq_category";
	$res = $wpdb->get_results($sql, ARRAY_A);
	foreach($res as $r)
	{ 
		if($cat == $r['faq_id'])
		{
			$sel ='selected="selected"';
		}
		else
		{
			$sel ='';
		}
		echo '<option value="'.$r['faq_id'].'" '.$sel.'>'.$r['faq_category'].'</option>';
	}	
	echo '</select>';
   ?>
   </label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
	$instance['category'] = $new_instance['category'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    
	
	extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;
 
    ?>
	<label for="s" class="assistive-text">Search</label>
	<input type="text" value="" name="webfaqsearchtext<?=$instance['category']?>" id="webfaqsearchtext<?=$instance['category']?>" />
	<input type="hidden" name="webfaqsearchcat<?=$instance['category']?>" id="webfaqsearchcat<?=$instance['category']?>" value="<?=$instance['category']?>" />
	<input type="button" name="webfaqsearchsubmit<?=$instance['category']?>" id="webfaqsearchsubmit<?=$instance['category']?>" value="Search" />
	<script type="text/javascript" >
	$(document).ready(function(){
	 $('#webfaqsearchsubmit<?=$instance['category']?>').click(function(){
		 $.ajax({
				url:"<?=admin_url('admin-ajax.php')?>",
				type: 'POST', 
				data: { 
					action		: 'displayAnswer',
					category	: $('#webfaqsearchcat<?=$instance['category']?>').val(),
					searchtxt	: $('#webfaqsearchtext<?=$instance['category']?>').val()
				},
				success: function(data) {
					$.fancybox({
                		'content' : data
           			 });
				}
			});
		});
	});
</script>
	<?PHP
 
    echo $after_widget;
  }
 
}

function displayAnswer()
{
 	if(is_numeric($_POST['category']))
	{
	global $wpdb;
	$master_table = $wpdb->prefix . "faq";
	$category = mysql_real_escape_string(sanitize_text_field($_POST['category']));
	$searchtxt = mysql_real_escape_string(sanitize_text_field($_POST['searchtxt']));
	if($category!=0)
	{
		$sql = "SELECT * FROM $master_table WHERE faq_category='".$category."' AND  faq_question LIKE '%".$searchtxt."%'";
	}
	else
	{
		$sql = "SELECT * FROM $master_table WHERE faq_question LIKE '%".$searchtxt."%'";
	}
	$question = $wpdb->get_results($sql);
	echo '<ul class="qa">';
	if($question)
	{
		foreach($question as $q)
		{
		echo '<li  class="question">'.$q->faq_question.'</li>';
		echo '<li class="answer">'.$q->faq_answer.'</li>';
		}
		
	}
	else
	{
		echo '<li class="answer">No records found</li>';
	}
	
	echo '</ul>';
	exit;
  }
  else
  {
  	echo 'Error';
	exit;
  }
}
  
add_action( 'widgets_init', create_function('', 'return register_widget("WebsiteFaqWidget");') );
add_action('wp_ajax_displayAnswer', 'displayAnswer');
add_action('wp_ajax_nopriv_displayAnswer', 'displayAnswer');
?>
