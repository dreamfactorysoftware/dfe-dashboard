<?php namespace DreamFactory\Enterprise\Dashboard\Managers;

use Carbon\Carbon;
use Dflydev\ApacheMimeTypes\PhpRepository;
use DreamFactory\Enterprise\Common\Managers\BaseManager;

class UploadManager extends BaseManager
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $disk;
    /**
     * @type \Dflydev\ApacheMimeTypes\PhpRepository
     */
    protected $mimeDetect;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Constructor
     *
     * @param \Illuminate\Contracts\Foundation\Application|null $app
     * @param \Dflydev\ApacheMimeTypes\PhpRepository            $mimeDetect
     */
    public function __construct($app, PhpRepository $mimeDetect)
    {
        parent::__construct($app);

        $this->disk = \Storage::disk(config('dashboard.upload-store'));
        $this->mimeDetect = $mimeDetect;
    }

    /**
     * Return files and directories within a folder
     *
     * @param string $folder
     *
     * @return array of [
     *    'folder' => 'path to current folder',
     *    'folderName' => 'name of just current folder',
     *    'breadCrumbs' => breadcrumb array of [ $path => $foldername ]
     *    'folders' => array of [ $path => $foldername] of each subfolder
     *    'files' => array of file details on each file in folder
     * ]
     */
    public function folderInfo($folder)
    {
        $folder = $this->cleanFolder($folder);

        $breadcrumbs = $this->breadcrumbs($folder);
        $slice = array_slice($breadcrumbs, -1);
        $folderName = current($slice);
        $breadcrumbs = array_slice($breadcrumbs, 0, -1);

        $subfolders = [];
        foreach (array_unique($this->disk->directories($folder)) as $subfolder) {
            $subfolders["/$subfolder"] = basename($subfolder);
        }

        $files = [];
        foreach ($this->disk->files($folder) as $path) {
            $files[] = $this->fileDetails($path);
        }

        return compact('folder',
            'folderName',
            'breadcrumbs',
            'subfolders',
            'files');
    }

    /**
     * Sanitize the folder name
     *
     * @param string $folder
     *
     * @return string
     */
    protected function cleanFolder($folder)
    {
        return '/' . trim(str_replace('..', '', $folder), '/');
    }

    /**
     * Return breadcrumbs to current folder
     *
     * @param string $folder
     *
     * @return array
     */
    protected function breadcrumbs($folder)
    {
        $folder = trim($folder, '/');
        $crumbs = ['/' => 'root'];

        if (empty($folder)) {
            return $crumbs;
        }

        $folders = explode('/', $folder);
        $build = '';
        foreach ($folders as $folder) {
            $build .= '/' . $folder;
            $crumbs[$build] = $folder;
        }

        return $crumbs;
    }

    /**
     * Return an array of file details for a file
     *
     * @param string $path
     *
     * @return array
     */
    protected function fileDetails($path)
    {
        $path = '/' . ltrim($path, '/');

        return [
            'name'     => basename($path),
            'fullPath' => $path,
            'webPath'  => $this->fileWebpath($path),
            'mimeType' => $this->fileMimeType($path),
            'size'     => $this->fileSize($path),
            'modified' => $this->fileModified($path),
        ];
    }

    /**
     * Return the full web path to a file
     *
     * @param string $path
     *
     * @return string
     */
    public function fileWebpath($path)
    {
        return url(rtrim(config('dashboard.upload-path'), '/') . '/' . ltrim($path, '/'));
    }

    /**
     * Return the mime type
     *
     * @param string $path
     *
     * @return null|string
     */
    public function fileMimeType($path)
    {
        return $this->mimeDetect->findType(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * Return the file size
     *
     * @param string $path
     *
     * @return int
     */
    public function fileSize($path)
    {
        return $this->disk->size($path);
    }

    /**
     * Return the last modified time
     *
     * @param string $path
     *
     * @return static
     */
    public function fileModified($path)
    {
        return Carbon::createFromTimestamp($this->disk->lastModified($path));
    }
}
