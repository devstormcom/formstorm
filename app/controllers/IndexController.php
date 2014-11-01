<?php
 namespace devStorm\Controllers;
 use devStorm\Controllers\BaseController;
 use devStorm\Models\Post;
/**
 * IndexPage
 *
 * @author Flavio Kleiber <flavio.kleiber@gentleman-informatik.ch>
 * @copyright (c) 2014 Flavio Kleiber, Gentleman Informatik
 * @package devstorm.controllers
 */
  
 class IndexController extends BaseController {
 	
 	public function indexAction() {
		$this->tag->prependTitle("&hearts; Home - ");
        $online = strtotime("- 30 days");
        $posts = Post::find(array('created >= :minutes:', 'bind' => array('minutes' => $online)));
        if($posts !== false) {
            $this->view->posts = $posts;
        }
	}
 }
?>