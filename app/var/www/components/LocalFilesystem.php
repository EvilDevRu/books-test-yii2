<?php

namespace app\components;

use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class LocalFilesystem extends Component
{
    public $path;
    protected $filesystem;

    /**
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        if ($this->path === null) {
            throw new InvalidConfigException('The "path" property must be set.');
        }

        $this->path = Yii::getAlias($this->path);
        $adapter = new LocalFilesystemAdapter(
            $this->path,
            PortableVisibilityConverter::fromArray([
                'file' => [
                    'public' => 0640,
                    'private' => 0604,
                ],
                'dir' => [
                    'public' => 0740,
                    'private' => 7604,
                ],
            ]),
            LOCK_EX,
            LocalFilesystemAdapter::DISALLOW_LINKS,
        );
        $this->filesystem = new \League\Flysystem\Filesystem($adapter);
    }

    /**
     * @param $name
     * @param $params
     * @return mixed
     */
    public function __call($name, $params)
    {
        return call_user_func_array([$this->filesystem, $name], $params);
    }
}