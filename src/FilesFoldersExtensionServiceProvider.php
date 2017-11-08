<?php namespace Defr\FilesFoldersExtension;

use Anomaly\Streams\Platform\Addon\AddonServiceProvider;
use Anomaly\Streams\Platform\Stream\Command\GetStream;
use Anomaly\SettingsModule\Setting\Contract\SettingRepositoryInterface;
use Illuminate\Routing\Router;

class FilesFoldersExtensionServiceProvider extends AddonServiceProvider
{

    /**
     * Boot the addon.
     */
    public function boot(SettingRepositoryInterface $settings)
    {
        $enabledStreams = $settings->value(
            'defr.extension.files_folders::enabled_streams',
            []
        );

        foreach ($enabledStreams as $identifier) {

            if (str_contains($identifier, '::')) {
                $temp = explode('::', $identifier);

                $parentSlug = array_get($temp, 0);
                $identifier = array_get($temp, 1);
            }

            $temp = explode('.', $identifier);

            $stream = $this->dispatch(new GetStream(
                studly_case(array_get($temp, 0)),
                studly_case(array_get($temp, 1))
            ));

            if (isset($parentSlug)) {
                $parent = $this->dispatch(new GetStream(
                    studly_case(array_get($temp, 0)),
                    studly_case($parentSlug)
                ));

                $parent->getEntryModel()::observe(
                    FilesFoldersExtensionObserver::class
                );

                return;
            }

            $stream->getEntryModel()::observe(
                FilesFoldersExtensionObserver::class
            );
        }
    }

}
