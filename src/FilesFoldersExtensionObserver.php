<?php namespace Defr\FilesFoldersExtension;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Entry\EntryObserver;
use Defr\FilesFoldersExtension\Command\FindOrNewFolder;

class FilesFoldersExtensionObserver extends EntryObserver
{

    /**
     * Fires just after entry was saved
     *
     * @param  EntryInterface  $entry  The entry
     */
    public function saved(EntryInterface $entry)
    {
        parent::saved($entry);

        if ($folder = $this->dispatch(new FindOrNewFolder($entry))) {
            $this->dispatch(new MoveImagesToFolder($entry, $folder));
        }
    }

}
