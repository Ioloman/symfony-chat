<?php

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UploaderHelper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static User|Proxy createOne(array $attributes = [])
 * @method static User[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static User|Proxy find($criteria)
 * @method static User|Proxy findOrCreate(array $attributes)
 * @method static User|Proxy first(string $sortedField = 'id')
 * @method static User|Proxy last(string $sortedField = 'id')
 * @method static User|Proxy random(array $attributes = [])
 * @method static User|Proxy randomOrCreate(array $attributes = [])
 * @method static User[]|Proxy[] all()
 * @method static User[]|Proxy[] findBy(array $attributes)
 * @method static User[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static User[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method User|Proxy create($attributes = [])
 */
final class UserFactory extends ModelFactory
{
    const IMAGES = ['pic1.jpg', 'pic2.png', 'pic3.jpeg'];

    private UploaderHelper $uploaderHelper;

    public function __construct(UploaderHelper $uploaderHelper)
    {
        parent::__construct();

        $this->uploaderHelper = $uploaderHelper;
    }

    public function adminUsers(): self
    {
        return $this->addState([
            'roles' => ["ROLE_ADMIN"],
            'email' => 'admin@sibers.com'
        ]);
    }

    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'password' => '1234',
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
//             ->afterInstantiate(function(User $user) {
//                 // copy fake images then "upload" them
//                 $pic = self::faker()->randomElement(self::IMAGES);
//
//                 $fs = new Filesystem();
//                 $targetPath = sys_get_temp_dir().'/'.$pic;
//                 $fs->copy(
//                     __DIR__.'/images/'.$pic,
//                     $targetPath
//                 );
//
//                $user->setProfilePic(
//                    $this->uploaderHelper->uploadUserProfilePic(
//                        new File($targetPath)
//                    )
//                );
//             })
        ;
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
