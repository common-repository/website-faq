<?PHP
/******
Plugin Name: Website FAQ
Plugin URI: http://www.quizpandit.com/
Description: With Website FAQ plugin you can add customer support FAQ search system in your website sidebar. This plugin allows you to add frequently asked questions and answers from admin interface.
Version: 1.0
Author: Anand Raju
Author URI: http://www.quizpandit.com
License: GPL
*******/
 
class Websitefaq
{
 
    function Websitefaq()
    {
        add_action('wp_head',  array(&$this, 'faq_script_files'));
		add_action('admin_menu', array(&$this, 'my_admin_menu'));
		register_activation_hook(__FILE__,array(&$this, 'faq_install')); 
		register_activation_hook(__FILE__,array(&$this, 'faq_install_data'));
		/* Runs on plugin deactivation*/
		register_deactivation_hook( __FILE__, array(&$this, 'faq_remove'));
		
		
		
		if(isset($_POST['savequestion']))
		{
			$this->saveQa($_POST);
		}
		
		if(isset($_POST['savecategory']))
		{
			$this->saveCategory($_POST);
		}
		
		if(isset($_POST['editquestion']))
		{
			$this->editQa($_POST);
		}
		
		if(isset($_POST['editcategory']))
		{
			$this->editCategory($_POST);
		}
    }
 
 	function faq_script_files()
	{
		$url = plugins_url('website-faq');
		echo '<script language="javascript" src="'.$url.'/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="'.$url.'/js/jquery.fancybox.js?v=2.0.6"></script>
	<link rel="stylesheet" type="text/css" href="'.$url.'/js/jquery.fancybox.css?v=2.0.6" media="screen" />
	<link rel="stylesheet" type="text/css" href="'.$url.'/css/website-qa.css"/>';
	}
	
	
    function my_admin_menu()
    {	
        add_menu_page('Website FAQ', 'Website FAQ', 'administrator', 'addnewfaq', array(&$this,'addnewfaq'));
        add_submenu_page('addnewfaq', 'All FAQS', 'All FAQS', 'administrator', 'listfaq', array(&$this,'listfaq'));
        add_submenu_page('addnewfaq', 'FAQ Categories', 'FAQ Categories', 'administrator', 'faqcategory', array(&$this,'faqcategory'));
 
    }
 
	
 
    function addnewfaq()
    {
       if ( !current_user_can('manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
	if(isset($_GET['editid']))
	{
	$qid = $_GET['editid'];
	global $wpdb;
	$category_table = $wpdb->prefix . "faq_category";
	$master_table = $wpdb->prefix . "faq";
	
	$sql = "SELECT * FROM $master_table WHERE faq_id=$qid";
	$question = $wpdb->get_row($sql, ARRAY_A);
	
	
	echo '<div class="wrap"><form action="" method="post">';
	echo '<p><h2>Edit Question &amp; Answer </h2></p>';
	echo '<p> <strong>Question Category</strong> <br><select name="category">';
	
	
	$sql = "SELECT * FROM $category_table ORDER BY faq_category";
	$res = $wpdb->get_results($sql, ARRAY_A);
	foreach($res as $r)
	{ 
		if($question['faq_category'] == $r['faq_id'])
		{
			$sel ='slected="selected"';
		}
		else
		{
			$sel = '';
		}
		echo '<option value="'.$r['faq_id'].'" $sel>'.$r['faq_category'].'</option>';
	}	
	echo '</select></p>';
	echo '<p> <strong>Question/Code</strong> <br><input type="text" id="question" name="question" value="'.$question['faq_question'].'" size="88"/></p>';
	echo '<div style="width:480px;"><strong>Answer</strong>';
	$settings= array(
	'textarea_rows' =>4,
	'tabindex' =>2,
	'tinymce' => array('width'=>'100%')
	
	);
	wp_editor($question['faq_answer'],'answer',$settings);
	echo '</div>';
	echo '<p><input type="hidden" name="editid" value="'.$qid.'"><input type="submit" name="editquestion" value="Save">&nbsp;<input type="reset" name="reset" value="Cancel"></p></form></div>';
	}
	else
	{
	echo '<div class="wrap"><form action="" method="post">';
	echo '<p><h2>Add New Question &amp; Answer </h2></p>';
	echo '<p> <strong>Question Category</strong> <br><select name="category">';
	
	global $wpdb;
	$category_table = $wpdb->prefix . "faq_category";
	$sql = "SELECT * FROM $category_table ORDER BY faq_category";
	$res = $wpdb->get_results($sql, ARRAY_A);
	foreach($res as $r)
	{ 
		echo '<option value="'.$r['faq_id'].'">'.$r['faq_category'].'</option>';
	}	
	echo '</select></p>';
	echo '<p> <strong>Question/Code</strong> <br><input type="text" id="question" name="question" size="88"/></p>';
	echo '<div style="width:480px;"><strong>Answer</strong>';
	$settings= array(
	'textarea_rows' =>4,
	'tabindex' =>2,
	'tinymce' => array('width'=>'100%')
	
	);
	wp_editor($answer='','answer',$settings);
	echo '</div>';
	echo '<p><input type="submit" name="savequestion" value="Save">&nbsp;<input type="reset" name="reset" value="Cancel"></p></form></div>';
	}
    }
	
		
	function faqcategory()
    {
       if ( !current_user_can('manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
	
	global $wpdb;
	$category_table = $wpdb->prefix . "faq_category";
	
	if(isset($_GET['delid']))
	{
	$query ="DELETE FROM $category_table WHERE faq_id=".$_GET['delid'];	
	$wpdb->query($query);
	}
	
	if(isset($_GET['editid']))
	{
	$sql = "SELECT * FROM $category_table WHERE faq_id=".$_GET['editid'];
	$res = $wpdb->get_row($sql, ARRAY_A);
	echo '<div class="wrap"><form action="" method="post">';
	echo '<p><h2>Edit Category </h2></p>';
	echo '<p> <strong>Question Category</strong> <br><input type="text" id="category" name="category" value="'.$res['faq_category'].'" size="88"/></p>';
	echo '<p><input type="hidden" name="editid" value="'.$_GET['editid'].'"><input type="submit" name="editcategory" value="Save">&nbsp;<input type="reset" name="reset" value="Cancel"></p></form></div>';
	}
	else
	{
	echo '<div class="wrap"><form action="" method="post">';
	echo '<p><h2>Add New Category </h2></p>';
	echo '<p> <strong>Question Category</strong> <br><input type="text" id="category" name="category" size="88"/></p>';
	echo '<p><input type="submit" name="savecategory" value="Save">&nbsp;<input type="reset" name="reset" value="Cancel"></p></form></div>';
	}
	
	
	
	echo '<table width="480" cellspacing="0" class="wp-list-table widefat fixed bookmarks" style="width:480px;">
	<thead>
	<tr>
		<th width="380">Categories</th>
		<th width="50">Edit</th>
		<th width="50">Delete</th></tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col"  class="manage-column column-categories"  style="">&nbsp;</th><th scope="col"  class="manage-column column-rel"  style="">&nbsp;</th><th scope="col"  class="manage-column column-visible sortable desc"  style="">&nbsp;</th></tr>
	</tfoot>

	<tbody id="the-list">';
	
	$sql = "SELECT * FROM $category_table ORDER BY faq_category";
	$res = $wpdb->get_results($sql, ARRAY_A);
	$i=0;
	foreach($res as $r)
	{ 
		if($i%2==0)
		{
			$s = 'class="alternate"';
		}
		else
		{
			$s="";
		}
		echo '<tr valign="middle" style=" "  $s>
			  <td style="width:370px;padding:5px">'.$r['faq_category'].'</td>
			  <td style="width:40px;padding:5px"><a href="?page=faqcategory&editid='.$r['faq_id'].'">Edit</a></td>
			  <td style="width:40px;padding:5px"><a href="?page=faqcategory&delid='.$r['faq_id'].'" onclick="return confirm(\'Are you sure to delete\')">Delete</a></td></tr>';
		$i++;
	}
	echo '</tbody></table>';
    }
 
    function listfaq()
    {
      global $wpdb;
	  $master_table = $wpdb->prefix . "faq";
	  $category_table = $wpdb->prefix . "faq_category";
	  
	  if(isset($_GET['delid']))
	  {
	  	$query ="DELETE FROM $master_table WHERE faq_id=".$_GET['delid'];	
		$wpdb->query($query);
	  }
	  
	    echo '<div class="wrap">';
	echo '<p><h2>Question &amp; Answers </h2></p>';
		echo '<table width="480" cellspacing="0" class="wp-list-table widefat fixed bookmarks" style="width:680px;">
	<thead>
	<tr>
		<th width="580">Question/Code</th>
		<th width="180">Category</th>
		<th width="50">Edit</th>
		<th width="50">Delete</th></tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col"  class="manage-column column-categories"  style="">&nbsp;</th><th scope="col"  class="manage-column column-rel"  style="">&nbsp;</th><th scope="col"  class="manage-column column-visible sortable desc"  style="">&nbsp;</th><th scope="col"  class="manage-column column-visible sortable desc"  style="">&nbsp;</th></tr>
	</tfoot>

	<tbody id="the-list">';
	
	$sql = "SELECT m.*,c.faq_category FROM $master_table m LEFT JOIN $category_table c ON m.faq_category = c.faq_id ORDER BY faq_id";
	$res = $wpdb->get_results($sql, ARRAY_A);
	$i=0;
	foreach($res as $r)
	{ 
		if($i%2==0)
		{
			$s = 'class="alternate"';
		}
		else
		{
			$s="";
		}
		echo '<tr valign="middle" style=" "  $s>
			  <td style="width:570px;padding:5px">'.$r['faq_question'].'</td>
			  <td style="width:170px;padding:5px">'.$r['faq_category'].'</td>
			  <td style="width:40px;padding:5px"><a href="?page=addnewfaq&editid='.$r['faq_id'].'">Edit</a></td>
			  <td style="width:40px;padding:5px"><a href="?page=listfaq&delid='.$r['faq_id'].'" onclick="return confirm(\'Are you sure to delete\')">Delete</a></td></tr>';
		$i++;
	}
	echo '</tbody></table>';
	
	echo '</div>';
    }
 
   
	
	function faq_install() {
	
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
    	$master_table = $wpdb->prefix . "faq";
		$category_table = $wpdb->prefix . "faq_category";
	
		$sql = "CREATE TABLE IF NOT EXISTS $master_table (`faq_id` bigint(20) NOT NULL AUTO_INCREMENT, `faq_question` varchar(300) NOT NULL, `faq_answer` text NOT NULL,
	  `faq_category` bigint(20) NOT NULL,PRIMARY KEY (`faq_id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		dbDelta($sql);
	
		$sql = "CREATE TABLE IF NOT EXISTS $category_table (`faq_id` bigint(20) NOT NULL AUTO_INCREMENT,`faq_category` varchar(200) NOT NULL, PRIMARY KEY (`faq_id`))
	 ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		dbDelta($sql);
	
	}

	function faq_install_data() {
	   	global $wpdb;
   		$default_category = "Deafult Category";
   		$table_name = $wpdb->prefix . "faq_category";
   		$wpdb->insert( $table_name, array( 'faq_category' => $default_category) );
	}

	function faq_remove() {
		global $wpdb;
		$master_table = $wpdb->prefix . "faq";
		$category_table = $wpdb->prefix . "faq_category";
		$wpdb->query("DROP TABLE $master_table");
		$wpdb->query("DROP TABLE $category_table");
	}
	
	function saveQa($arr)
	{
		$question = $arr['question'];
		$category = $arr['category'];
		$answer = $arr['answer'];
		
		global $wpdb;
		$master_table = $wpdb->prefix . "faq";
		$wpdb->insert( $master_table, array( 'faq_question' =>$question, 'faq_answer' =>$answer, 'faq_category' => $category ) );
		header("location:admin.php?page=listfaq");
	}
	
	function editQa($arr)
	{
		$question = $arr['question'];
		$category = $arr['category'];
		$answer = $arr['answer'];
		$editid = $arr['editid'];
		
		global $wpdb;
		$master_table = $wpdb->prefix . "faq";
		$wpdb->update( $master_table, array( 'faq_question' =>$question, 'faq_answer' =>$answer, 'faq_category' => $category ),array('faq_id' => $editid) );
		header("location:admin.php?page=listfaq");
	}
	
	function saveCategory($arr)
	{

		$category = $arr['category'];
		global $wpdb;
		$master_table = $wpdb->prefix . "faq_category";
		$wpdb->insert( $master_table, array( 'faq_category' =>$category) );
		header("location:admin.php?page=faqcategory");
	}
	
	function editCategory($arr)
	{

		$category = $arr['category'];
		$editid = $arr['editid'];
		global $wpdb;
		$master_table = $wpdb->prefix . "faq_category";
		$wpdb->update( $master_table, array( 'faq_category' =>$category),array('faq_id' => $editid) );
		header("location:admin.php?page=faqcategory");
	}
 
}
 
 
$mybackuper = &new Websitefaq();//instance of the plugin class