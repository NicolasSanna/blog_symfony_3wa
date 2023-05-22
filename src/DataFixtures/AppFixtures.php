<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Article;
use \DateTimeImmutable;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = new User();
        $user   ->setFirstname('Nicolas')
                ->setLastname('SANNA')
                ->setPassword(password_hash('admin', PASSWORD_DEFAULT))
                ->setCreatedAt(new DateTimeImmutable())
                ->setRoles(['ROLE_ADMIN'])
                ->setEmail('nico13sanna@gmail.com');
        
        $categoryOne = new Category();
        $categoryOne->setLabel('Histoire');

        $categoryTwo = new Category();
        $categoryTwo->setLabel('Philosophie');

        $article = new Article();
        $article    ->setTitle('Histoire de la Rome Antique')
                    ->setDescription('Histoire de Jules César')
                    ->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris hendrerit ipsum at lobortis accumsan. Etiam varius et ex sit amet pretium. In nisi orci, aliquam in facilisis eget, malesuada et nulla. Nulla mattis ornare dolor sit amet semper. In et sollicitudin dolor. Ut et nibh dolor. Nunc eu tincidunt felis, eu euismod elit. Proin nisl lectus, varius et molestie eu, consectetur eu eros. Duis quis dui rhoncus tellus faucibus blandit vitae vel leo.
                                Duis vel leo sapien. Sed lacus lacus, imperdiet ac auctor at, lobortis vel ante. Nunc pharetra eu metus quis luctus. Suspendisse finibus tortor sit amet tristique tincidunt. Fusce tempor maximus orci nec tempor. Cras non nisl posuere, ultrices dolor ut, ultrices turpis. Mauris ut leo pulvinar, ullamcorper dui et, gravida neque. Mauris eget vehicula neque, sed dapibus orci. Quisque in massa et urna volutpat efficitur. Cras cursus facilisis orci et ullamcorper.    
                                Nullam tempor mattis elit, eu sodales sapien bibendum ut. In pharetra elit eleifend leo vehicula, ut rutrum enim posuere. Suspendisse et arcu efficitur, hendrerit diam et, imperdiet ante. Ut blandit justo ante, eget vehicula ex tincidunt id. Ut porta imperdiet leo, pulvinar porta leo porta et. Fusce ultricies turpis eu justo fringilla, ut molestie sem dignissim. Integer elementum tincidunt magna, congue volutpat justo tempor vitae. Fusce semper sit amet nunc ac lacinia. Vestibulum sit amet dolor elit. Quisque imperdiet laoreet erat at dignissim. Morbi mattis lacus risus, sit amet tincidunt justo malesuada eget. Mauris blandit, quam at sodales ornare, nulla nibh rutrum lacus, eget elementum quam mauris eget lorem. Sed eu nisl quis libero viverra faucibus. Aenean bibendum eros sit amet ipsum accumsan molestie. Quisque quis nisl urna.               
                                Nunc facilisis accumsan nisl, eget congue est. Ut interdum feugiat mi, eget facilisis quam consequat sed. Nunc imperdiet suscipit eros, non finibus mi dictum non. Nullam interdum pulvinar est nec luctus. Phasellus ut enim id tellus fringilla mollis. Sed tristique at augue eu malesuada. Suspendisse potenti. Vestibulum a varius quam. Nam mollis est vel rutrum imperdiet. Etiam et dui nec dolor euismod aliquam.')
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setAuthor($user)
                    ->addCategory($categoryOne)
                    ->addCategory($categoryTwo)
                    ->setImage('');

        $comment = new Comment();
        $comment    ->setContent('Un super Article !')
                    ->setAuthor($user)
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setArticle($article);

        $manager->persist($user);
        $manager->persist($categoryOne);
        $manager->persist($categoryTwo);
        $manager->persist($article);

        $manager->persist($comment);

        $manager->flush();
    }
}
