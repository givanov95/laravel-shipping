<?php

declare(strict_types=1);

namespace Givanov95\Shipping;

use Givanov95\Shipping\Contracts\DeliveryMethod;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use LogicException;

/**
 * The delivery methods a shop can offer. A package registers its method from its service
 * provider; which of them are offered, and in what order, is up to the shop.
 *
 * A method is built by the container the first time it is asked for, so registering one
 * costs nothing on a request that never reaches the checkout.
 */
final class DeliveryMethods
{
    /**
     * @var array<string, array{class: class-string<DeliveryMethod>, label: string}>
     */
    private array $methods = [];

    public function __construct(private readonly Container $container)
    {
    }

    /**
     * A second registration under the same key replaces the first.
     *
     * @param  string  $key  what DeliveryMethod::key() of the class returns
     * @param  class-string<DeliveryMethod>  $class
     * @param  string  $label  what the shop calls the method; the shop translates it
     */
    public function register(string $key, string $class, string $label): void
    {
        if (! is_a($class, DeliveryMethod::class, true)) {
            throw new InvalidArgumentException(sprintf('%s does not implement %s.', $class, DeliveryMethod::class));
        }

        $this->methods[$key] = ['class' => $class, 'label' => $label];
    }

    public function has(string $key): bool
    {
        return isset($this->methods[$key]);
    }

    /**
     * @return list<string> in the order of registration
     */
    public function keys(): array
    {
        return array_map(strval(...), array_keys($this->methods));
    }

    /**
     * @return array<string, string> the label of every registered method, by key
     */
    public function labels(): array
    {
        return array_map(fn (array $method) => $method['label'], $this->methods);
    }

    /**
     * Null for a key nothing is registered under.
     */
    public function get(string $key): ?DeliveryMethod
    {
        if (! $this->has($key)) {
            return null;
        }

        $method = $this->container->make($this->methods[$key]['class']);

        // The key ends in the Destination the method builds: under another one an order would
        // name one method and its delivery another.
        if ($method->key() !== $key) {
            throw new LogicException(sprintf('%s is registered as "%s" but calls itself "%s".', $method::class, $key, $method->key()));
        }

        return $method;
    }
}
