<?php
namespace StoreFinder\Controller;

/**
 * Dashboard controller
 *
 * General controller for dashboard related functions.
 *
 * @package		Controllers
 * @category	App
 * @version		0.01
 */

class DashboardController extends BaseController {

    public function __construct()
    {
		if(\Auth::check())
		{
			$this->parent_user_id = (\Auth::user()->parent_id == 0) ? \Auth::user()->id : \Auth::user()->parent_id;
		}
		else
		{
			$this->parent_user_id = 0;
		}

		$this->cats = \StoreFinder\Model\Category::where('user_id', '=', $this->parent_user_id)->orderBy('name', 'ASC')->get();
    }

    /**
     * Dashboard view
     */
	public function showDashboard()
	{
		$oDeleted = \StoreFinder\Model\Category::onlyTrashed()->where('user_id', $this->parent_user_id)->get();

		$oTrash = NULL;

		if(isset($_GET['trash']))
		{
			$oTrash = \StoreFinder\Model\Category::onlyTrashed()->where('user_id', $this->parent_user_id)->forceDelete();
		}

		$categories = \StoreFinder\Model\User::find($this->parent_user_id)->categories()->count();
		//$active_categories = \StoreFinder\Model\User::find($this->parent_user_id)->categories()->where('active', '=', 1)->count();
		//$inactive_categories = \StoreFinder\Model\User::find($this->parent_user_id)->categories()->where('active', '=', 0)->count();
		$active_items = \StoreFinder\Model\Item::where('user_id', '=', $this->parent_user_id)->where('active', '=', 1)->count();
		$inactive_items = \StoreFinder\Model\Item::where('user_id', '=', $this->parent_user_id)->where('active', '=', 0)->count();
		$options = \StoreFinder\Model\Option::where('user_id', '=', $this->parent_user_id)->count();

		$error = \Session::get('error', false);
		$message = \Session::get('message', false);

		return \View::make('app.dashboard.main')
			->with('oCats', $this->cats)
			->with('oDeleted', $oDeleted)
			->with('oTrash', $oTrash)
			->with('parent_user_id', $this->parent_user_id)
			->with('categories', $categories)
			->with('active_items', $active_items)
			->with('inactive_items', $inactive_items)
			->with('options', $options)
			->with('error', $error)
			->with('message', $message);
	}
}