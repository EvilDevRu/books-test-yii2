<?php

namespace app\components;

use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Filesystem\FilesystemException;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class GlideComponent extends Component
{
    public $sourcePath;
    public $cachePath;
    public $baseUrl = '/images';

    public function __construct(
        protected \League\Glide\Server $server,
        array $config = [],
    )
    {
        parent::__construct($config);
    }

    /**
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        if ($this->sourcePath === null) {
            throw new InvalidConfigException('The "sourcePath" property must be set.');
        }

        if ($this->cachePath === null) {
            throw new InvalidConfigException('The "cachePath" property must be set.');
        }

        $this->sourcePath = Yii::getAlias($this->sourcePath);
        $this->cachePath = Yii::getAlias($this->cachePath);

        if (!is_dir($this->sourcePath)) {
            mkdir($this->sourcePath, 0755, true);
        }

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
    }

    /**
     * @throws FilesystemException
     * @throws FileNotFoundException
     */
    public function makeImage($path, array $params = []): string
    {
        return $this->baseUrl . $this->server->makeImage($path, $params);
    }
}