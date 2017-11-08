<?php namespace Defr\FilesFoldersExtension\Support\Handler;

use Anomaly\CheckboxesFieldType\CheckboxesFieldType;
use Anomaly\FileFieldType\FileFieldType;
use Anomaly\FilesFieldType\FilesFieldType;
use Anomaly\Streams\Platform\Stream\Command\GetStream;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface;

/**
 * Class for get streams having files options.
 */
class StreamsHaveFiles
{

    /**
     * Handle the command
     *
     * @param   CheckboxesFieldType        $fieldType  The field type
     * @param   StreamRepositoryInterface  $streams    The streams
     * @return  array
     */
    public function handle(
        CheckboxesFieldType $fieldType,
        StreamRepositoryInterface $streams
    )
    {
        $filterStreams = function (StreamInterface $stream) {

            $slugs = $stream->getRelationshipAssignments()->fieldSlugs();

            foreach ($slugs as $slug) {
                /* @var FieldType $fieldType */
                $fieldType = $stream->getFieldType($slug);

                if ($fieldType instanceof FileFieldType ||
                    $fieldType instanceof FilesFieldType) {
                    return true;
                }
            }

            return false;
        };

        $hidden = $streams->hidden(true)->filter($filterStreams)
            ->mapWithKeys(
                function (StreamInterface $stream) {

                    $namespace = $stream->getNamespace();
                    $name      = ucfirst($namespace) . ' ' . $stream->getName();
                    $slug      = $stream->getSlug();
                    $key       = $namespace . '.' . $slug;

                    preg_match(
                        '/.*_(?<parent>\w+)/',
                        $slug,
                        $matches,
                        PREG_OFFSET_CAPTURE,
                        0
                    );

                    return [array_get($matches, 'parent.0') . '::' . $key => $name];
                }
            )->toArray();

        $visible = $streams->hidden(false)->filter($filterStreams)
            ->mapWithKeys(
                function (StreamInterface $stream) {

                    $namespace = $stream->getNamespace();
                    $name      = $stream->getName();
                    $slug      = $stream->getSlug();
                    $key       = $namespace . '.' . $slug;

                    return [$key => $name];
                }
            )->toArray();

        $fieldType->setOptions(array_merge($hidden, $visible));
    }

}
