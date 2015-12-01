<?php namespace Wpb\StringBladeCompiler;

use App;
use ArrayAccess;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\View;
use Illuminate\View\Engines\CompilerEngine;
use StringCompilerException;
use Wpb\StringBladeCompiler\Compilers\StringBladeCompiler;


class StringView extends \Illuminate\View\View implements ArrayAccess, ViewContract {

    protected $template_field = 'template';
    protected $compiler;


    public function __construct()
    {
        $cache = storage_path('framework/views');
        $this->compiler = new StringBladeCompiler(App::make('files'), $cache);

        $this->engine = new CompilerEngine($this->compiler);
    }

    /**
     * Returns the current compiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }

    /**
     * Directive method.
     *
     * @param $name
     * @param $handler
     */
    public function directive( $name, $handler )
    {
        $this->compiler->directive( $name, $handler );
    }

    /**
     * Get a evaluated view contents for the given view.
     *
     * @param  object|array $view
     * @param  array $data
     * @param  array $mergeData
     * @throws StringCompilerException
     * @return \Illuminate\View\View
     */
    public function make($view, $data = array(), $mergeData = array())
    {

        // if array convert to object
        $view = ( object ) $view;

        /* validate the object */

        // timestamp for the compiled template cache
        // this needs to be updated if the actual template data changed
        if (!isset($view->updated_at)) {
            throw new StringCompilerException('Missing template last modified timestamp.');
        } else {
            if (!$this->is_timestamp($view->updated_at)) {
                throw new StringCompilerException('Template last modified timestamp appears to be invalid.');
            }
            /*
           * Note: a timestamp of 0 translates to force recompile of the template.
           */
        }

        // this is the actually blade template data
        if (!isset($view->template)) {
            throw new StringCompilerException('No template data was provided.');
        }

        // each template requires a unique cache key
        if (!isset($view->cache_key)) {
            throw new StringCompilerException('Missing unique template cache string.');
        }

        $this->path = $view;
        $this->data = array_merge($mergeData, $this->parseData($data));

        return $this;
    }

    /**
     * Checks if a string is a valid timestamp.
     * from https://gist.github.com/sepehr/6351385
     *
     * @param string $timestamp Timestamp to validate.
     *
     * @return bool
     */
    function is_timestamp($timestamp)
    {
        $check = (is_int($timestamp) OR is_float($timestamp))
            ? $timestamp
            : (string)(int)$timestamp;

        return ($check === $timestamp)
        AND ((int)$timestamp <= PHP_INT_MAX)
        AND ((int)$timestamp >= ~PHP_INT_MAX);
    }

    /**
     * Parse the given data into a raw array.
     *
     * @param  mixed $data
     * @return array
     */
    protected function parseData($data)
    {
        return $data instanceof Arrayable ? $data->toArray() : $data;
    }

    /**
     * Get the string contents of the view.
     *
     * @param  \Closure $callback
     * @return string
     */
    public function render(Closure $callback = null)
    {
        $contents = $this->renderContents();

        $response = isset($callback) ? $callback($this, $contents) : null;

        // Once we have the contents of the view, we will flush the sections if we are
        // done rendering all views so that there is nothing left hanging over when
        // another view is rendered in the future by the application developers.
        View::flushSectionsIfDoneRendering();

        return $response ?: $contents;
    }

    /**
     * Get the contents of the view instance.
     *
     * @return string
     */
    protected function renderContents()
    {
        // We will keep track of the amount of views being rendered so we can flush
        // the section after the complete rendering operation is done. This will
        // clear out the sections for any separate views that may be rendered.
        View::incrementRender();

        $contents = $this->getContents();

        // Once we've finished rendering the view, we'll decrement the render count
        // so that each sections get flushed out next time a view is created and
        // no old sections are staying around in the memory of an environment.
        View::decrementRender();

        return $contents;
    }

    /**
     * @return string
     */
    protected function getContents()
    {
        /**
         * This property will be added to models being compiled with StringView
         * to keep track of which field in the model is being compiled
         */
        $this->path->__string_blade_compiler_template_field = $this->template_field;

        return parent::getContents();
    }

    /**
     * Add a view instance to the view data.
     *
     * @param  string $key
     * @param  string $view
     * @param  array $data
     * @return \Illuminate\View\View
     */
    public function nest($key, $view, array $data = array())
    {
        return $this->with($key, View::make($view, $data));
    }

    /**
     * Determine if a piece of data is bound.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Get a piece of bound data to the view.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->data[$key];
    }

    /**
     * Set a piece of data on the view.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->with($key, $value);
    }

    /**
     * Unset a piece of data from the view.
     *
     * @param  string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Register a custom Blade compiler.
     *
     * @param  \Closure $compiler
     * @return void
     */
    public function extend(Closure $compiler)
    {
        $this->engine->getCompiler()->extend($compiler);
    }

    /**
     * Sets the raw tags used for the compiler.
     *
     *
     * @param  string $openTag
     * @param  string $closeTag
     * @return void
     */
    public function setRawTags($openTag, $closeTag)
    {
        $this->engine->getCompiler()->setRawTags($openTag, $closeTag);
    }

    /**
     * Sets the escaped content tags used for the compiler.
     *
     * @param  string $openTag
     * @param  string $closeTag
     * @return void
     */
    public function setEscapedContentTags($openTag, $closeTag)
    {
        $this->setContentTags($openTag, $closeTag , true);
    }

    /**
     * Sets the content tags used for the compiler.
     *
     * @param  string $openTag
     * @param  string $closeTag
     * @param  bool $escaped
     * @return void
     */
    public function setContentTags($openTag, $closeTag, $escaped = false)
    {
        $this->engine->getCompiler()->setContentTags($openTag, $closeTag, $escaped);
    }

    /**
     * Set the echo format to be used by the compiler.
     *
     * @param  string  $format
     * @return void
     */
    public function setEchoFormat($format)
    {
        $this->engine->getCompiler()->setEchoFormat($format);
    }

    /**
     * Get the data bound to the view instance.
     *
     * @return array
     */
    protected function gatherData()
    {
        $data = array_merge(View::getShared(), $this->data);

        foreach ($data as $key => $value) {
            if ($value instanceof Renderable) {
                $data[$key] = $value->render();
            }
        }

        return $data;
    }

}
