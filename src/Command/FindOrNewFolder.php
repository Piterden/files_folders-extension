<?php namespace Defr\FilesFoldersExtension\Command;

use Anomaly\FilesModule\Disk\Contract\DiskRepositoryInterface;
use Anomaly\FilesModule\Folder\Contract\FolderRepositoryInterface;
use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;

class FindOrNewFolder
{

    /**
     * Entry model
     *
     * @var EntryInterface
     */
    protected $entry;

    /**
     * Create an instance of FindOrNewFolder class
     *
     * @param  EntryInterface  $entry  The entry
     */
    public function __construct(EntryInterface $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Handle the command
     *
     * @param FolderRepositoryInterface $folders The folders
     * @param DiskRepositoryInterface   $disks   The disks
     */
    public function handle(
        FolderRepositoryInterface $folders,
        DiskRepositoryInterface $disks
    )
    {
        /* @var DiskInterface $disk */
        $disk = $disks->findBySlug('local');

        $title = $this->entry->getId() . get_class($this->entry);
        $hash  = md5($title);

        /* @var FolderInterface $folder */
        if ($folder = $folders->findBySlug($hash)) {
            return $folder;
        }

        return $folders->create([
            'en'            => [
                'name'        => $title,
                'description' => "A folder for \"{$title}\" images.",
            ],
            'slug'          => $hash,
            'disk'          => $disk,
            'allowed_types' => [
                'png',
                'jpeg',
                'jpg',
                'gif',
                'svg',
            ],
        ]);
    }

}
