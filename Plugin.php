<?php namespace Pi\Livestream;

use Backend;
use System\Classes\PluginBase;

/**
 * Plugin Information File
 *
 * @link https://docs.octobercms.com/3.x/extend/system/plugins.html
 */
class Plugin extends PluginBase
{
    /**
     * pluginDetails about this plugin.
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'livestream',
            'description' => 'No description provided yet...',
            'author'      => 'pi',
            'icon'        => 'icon-leaf',
         ];
    }

    /**
     * register method, called when the plugin is first registered.
     */
    public function register()
    {
        //
    }

    /**
     * boot method, called right before the request route.
     */
    public function boot()
    {
    }

    /**
     * registerComponents used by the frontend.
     */
    public function registerComponents()
    {
        return [  ]; // Remove this line to activate

        return [
            'Pi\Livestream\Components\MyComponent' => 'myComponent',
         ];
    }

    /**
     * registerPermissions used by the backend.
     */
    public function registerPermissions()
    {
        return [  ]; // Remove this line to activate

        return [
            'pi.livestream.some_permission' => [
                'tab'   => 'livestream',
                'label' => 'Some permission',
             ],
         ];
    }

    /**
     * registerNavigation used by the backend.
     */
    public function registerNavigation()
    {
        return [  ]; // Remove this line to activate

        return [
            'livestream' => [
                'label'       => 'livestream',
                'url'         => Backend::url('pi/livestream/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => [ 'pi.livestream.*' ],
                'order'       => 500,
             ],
         ];
    }
}
