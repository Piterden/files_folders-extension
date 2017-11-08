<?php namespace Defr\FilesFoldersExtension\Command;

use Anomaly\FileFieldType\FileFieldType;
use Anomaly\FilesFieldType\FilesFieldType;
use Anomaly\FilesModule\File\Contract\FileInterface;
use Anomaly\FilesModule\File\FileCollection;
use Anomaly\FilesModule\Folder\Contract\FolderInterface;
use Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface;
use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;

/**
 * Class for move images to a new folder.
 */
class MoveImagesToFolder
{

    /**
     * Entry model
     *
     * @var  EntryInterface
     */
    protected $entry;

    /**
     * Folder model
     *
     * @var  FolderInterface
     */
    protected $folder;

    /**
     * Create an instance of MoveImagesToFolder class
     *
     * @param  EntryInterface   $entry   The entry
     * @param  FolderInterface  $folder  The folder
     */
    public function __construct(
        EntryInterface $entry,
        FolderInterface $folder
    )
    {
        $this->entry  = $entry;
        $this->folder = $folder;
    }

    /**
     * Handle the command
     */
    public function handle()
    {
        $this->entry->getStream()
            ->getRelationshipAssignments()
            ->filter(
                function (AssignmentInterface $assignment) {
                    $fieldType = $assignment->getFieldType();

                    return $fieldType instanceof FileFieldType ||
                        $fieldType instanceof FilesFieldType;
                }
            )
            ->each(
                function (AssignmentInterface $assignment) {
                    $files = $assignment->getFieldType()->getRelation()->get();

                    if (!$files instanceof FileCollection) {
                        $files = new FileCollection([$files]);
                    }

                    $this->changeFolder($files);
                }
            );
    }

    /**
     * Change folder for file collection
     *
     * @param  FileCollection  $files  The files
     */
    protected function changeFolder(FileCollection $files) {
        $files->each(
            function (FileInterface $file) {
                $folderId = $this->folder->getId();

                if ($file->getFolder()->getId() == $folderId) {
                    return;
                }

                $file->update([
                    'folder_id' => $folderId,
                ]);
            }
        );
    }

}
