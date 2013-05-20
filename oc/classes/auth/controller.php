<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Front end controller for OC user/admin auth in the app
 *
 * @package    OC
 * @category   Controller
 * @author     Chema <chema@garridodiaz.com>
 * @copyright  (c) 2009-2013 Open Classifieds Team
 * @license    GPL v3
 */

class Auth_Controller extends Controller
{

	/**
	 *
	 * Contruct that checks you are loged in before nothing else happens!
	 */
	function __construct(Request $request, Response $response)
	{
		// Assign the request to the controller
		$this->request = $request;

		// Assign a response to the controller
		$this->response = $response;


		//login control, don't do it for auth controller so we dont loop
		if ($this->request->controller()!='auth')
		{
			
			$url_bread = Route::url('oc-panel',array('controller'  => 'home'));
			Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Panel'))->set_url($url_bread));
				
			//check if user is login
			if (!Auth::instance()->logged_in( $request->controller(), $request->action(), $request->directory()))
			{
				Alert::set(Alert::ERROR, __('You do not have permissions to access '.$request->controller().' '.$request->action()));
				$url = Route::get('oc-panel')->uri(array(
													 'controller' => 'auth', 
													 'action'     => 'login'));
				$this->request->redirect($url);
			}

            if (Theme::$theme != Core::config('appearance.theme')) 
                Theme::initialize(Core::config('appearance.theme'));

		}

		//the user was loged in and with the right permissions
        parent::__construct($request,$response);
		
		
	}


	/**
	 * Initialize properties before running the controller methods (actions),
	 * so they are available to our action.
	 * @param  string $template view to use as template
	 * @return void           
	 */
	public function before($template = NULL)
	{
        $this->maintenance();
	
		if($this->auto_render===TRUE)
		{
			// Load the template
			$this->template = ($template===NULL)?'oc-panel/main':$template;
			$this->template = View::factory($this->template);
				
			// Initialize empty values
			$this->template->title            = __('Panel').' - '.core::config('general.site_name');
			$this->template->meta_keywords    = '';
			$this->template->meta_description = '';
			$this->template->meta_copywrite   = 'Open Classifieds '.Core::version;
			$this->template->header           = View::factory('oc-panel/header');
			$this->template->content          = '';
			$this->template->footer           = View::factory('oc-panel/footer');
			$this->template->styles           = array();
			$this->template->scripts          = array();
			$this->template->user 			  = Auth::instance()->get_user();

            //other color
            if (Theme::get('admin_theme')!='bootstrap' AND Theme::get('admin_theme')!='')
            {
                Theme::$styles               = array(                                  
                                                'http://netdna.bootstrapcdn.com/bootswatch/2.3.1/'.Theme::get('admin_theme').'/bootstrap.min.css' => 'screen',
                                                'http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-responsive.min.css' => 'screen',
                                                'css/chosen.css' => 'screen', 
                                                'css/jquery.sceditor.min.css' => 'screen',
                                                );
            }
            //default theme
            else
            {
                Theme::$styles                    = array('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css' => 'screen',
                                                    'css/jquery.sceditor.min.css' => 'screen',
                                                      'css/chosen.css'=>'screen');
            }


            Theme::$scripts['footer']		  = array('http://code.jquery.com/jquery-1.9.1.min.js',	
													  'http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js', 
												      'js/chosen.jquery.min.js',
                                                      'js/oc-panel/theme.init.js',
                                                      'js/jquery.sceditor.min.js'
                                                      );
		}
		
		
	}


}