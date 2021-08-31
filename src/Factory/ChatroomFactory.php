<?php

namespace App\Factory;

use App\Entity\Chatroom;
use App\Repository\ChatroomRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static Chatroom|Proxy createOne(array $attributes = [])
 * @method static Chatroom[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static Chatroom|Proxy find($criteria)
 * @method static Chatroom|Proxy findOrCreate(array $attributes)
 * @method static Chatroom|Proxy first(string $sortedField = 'id')
 * @method static Chatroom|Proxy last(string $sortedField = 'id')
 * @method static Chatroom|Proxy random(array $attributes = [])
 * @method static Chatroom|Proxy randomOrCreate(array $attributes = [])
 * @method static Chatroom[]|Proxy[] all()
 * @method static Chatroom[]|Proxy[] findBy(array $attributes)
 * @method static Chatroom[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Chatroom[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ChatroomRepository|RepositoryProxy repository()
 * @method Chatroom|Proxy create($attributes = [])
 */
final class ChatroomFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://github.com/zenstruck/foundry#model-factories)
            'status' => self::faker()->boolean(70) ? 'opened' : 'closed',
            'type' => self::faker()->boolean(70) ? 'private' : 'public',
            'name' => self::faker()->name(),
            'updatedAt' => self::faker()->dateTimeBetween('-1 month'),
            'createdAt' => self::faker()->dateTime('-1 month'),
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Chatroom $chatroom) {})
        ;
    }

    protected static function getClass(): string
    {
        return Chatroom::class;
    }
}
