<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Services;

use ArrayAccess;
use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use Mfc\PasswordManager\Configuration\Configuration;
use Mfc\PasswordManager\Platform\Platform;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Yaml\Yaml;
use Traversable;

/**
 * Class ConfigurationService
 * @package Mfc\PasswordManager\Services
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class ConfigurationService implements IteratorAggregate, ArrayAccess
{
    private array $config = [];
    private readonly PropertyAccessor $accessor;

    /**
     * ConfigurationService constructor.
     */
    public function __construct(private readonly SerializerInterface $serializer)
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function loadConfiguration(string $file): void
    {
        if (!is_readable($file)) {
            throw new InvalidArgumentException(sprintf('The file "%s" does not exist.', $file));
        }

        $configuration = new Configuration();

        $content = Yaml::parseFile($file);
        $processor = new Processor();
        $this->config = $processor->processConfiguration($configuration, $content);

        $this->config['config_path'] = realpath(dirname($file));
        $this->config['users_path'] = realpath($this->config['config_path'] . '/users/');

        $oldcwd = getcwd();
        chdir($this->config['config_path']);
        $this->config['platform_file'] = realpath($this->config['platform_file']);

        $this->loadPlatformFile($this->config['platform_file']);

        chdir($oldcwd);
    }

    private function loadPlatformFile(string $platformFile): void
    {
        if (!file_exists($platformFile) || !is_readable($platformFile)) {
            throw new InvalidArgumentException(sprintf('The platform file "%s" does not exist.', $platformFile));
        }

        $content = file_get_contents($platformFile);
        $platforms = $this->serializer->deserialize($content, Platform::class . '[]', 'yaml');

        $this->config['platforms'] = $platforms;
    }

    /**
     * Retrieve an external iterator
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->config);
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return bool true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->accessor->isReadable($this->config, $offset);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->accessor->getValue($this->config, $offset);
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->accessor->setValue($this->config, $offset, $value);
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->accessor->setValue($this->config, $offset, null);
    }
}
