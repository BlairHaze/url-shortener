<?php

namespace App\DataFixtures;

use App\Entity\Tags;
use App\Entity\Url;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UrlFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(60, 'urls', function () {
            $url = new Url();
            $url->setLongName($this->faker->url);
            $url->setShortName($this->faker->regexify('[a-zA-Z0-9]{6}'));
            $url->setCreateTime(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $url->setIsBlocked($this->faker->boolean(20));

            if ($url->isIsBlocked()) {
                $url->setBlockTime(
                    \DateTimeImmutable::createFromMutable(
                        $this->faker->dateTimeBetween('-1 days', '+100 days')
                    )
                );
            }

            /** @var array<array-key, Tags> $tags */
            $tags = $this->getRandomReferences('tags', $this->faker->numberBetween(0, 5));
            foreach ($tags as $tag) {
                $url->addTag($tag);
            }

            return $url;
        });

        $this->manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TagsFixtures::class,
        ];
    }
}
