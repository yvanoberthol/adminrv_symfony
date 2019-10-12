<?php

namespace App\DataFixtures;

use App\Entity\ImageMedecin;
use App\Entity\Medecin;
use App\Entity\Specialite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class MedecinFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $image1 = new ImageMedecin();
        $image1->setAlt('yvano berthol');
        $image1->setUrl('imgs/yvanoberthol.png');
        $manager->persist($image1);

        $image2 = new ImageMedecin();
        $image2->setAlt('marie regine');
        $image2->setUrl('imgs/marie.png');
        $manager->persist($image2);

        $liste_specialites = [
            [
                'name' => 'dermatologie',
                'description' => 'c\'est la science qui étudie la peau'
            ],
            [
                'name' => 'ophtalmologie',
                'description' => 'qui étudie les yeux'
            ],
            [
                'name' => 'rhumatologie',
                'description' => 'Etude des os touchés'
            ],
            [
                'name' => 'Biologie',
                'description' => 'etude de l\'environnement'
            ]
        ];
        foreach ($liste_specialites as $specialite) {
            $ob_specialite = new Specialite();
            $ob_specialite->setNom($specialite['name']);
            $ob_specialite->setDescription($specialite['description']);
            $manager->persist($ob_specialite);

            $specialites[] = $ob_specialite;
        }

        $liste_medecins = [
            [
                'nom' => 'yvano',
                'prenom' => 'berthol',
                'matricule' => 'M001',
                'email' => 'yvanoberthol@gmail.com',
                'telephone' => '690735187',
                'ville' => 'Douala',
                'date_naissance' => new \DateTime('1997-05-11'),
                'image_id' => $image1
            ],
            [
                'nom' => 'marie',
                'prenom' => 'regine',
                'matricule' => 'M002',
                'email' => 'marieemmagam@gmail.com',
                'telephone' => '699510052',
                'ville' => 'Douala',
                'date_naissance' => new \DateTime('1975-09-21'),
                'image_id' => $image2
            ]
        ];
        foreach ($liste_medecins as $medecin) {
            $ob_medecin = new Medecin();
            $ob_medecin->setNom($medecin['nom']);
            $ob_medecin->setPrenom($medecin['prenom']);
            $ob_medecin->setMatricule($medecin['matricule']);
            $ob_medecin->setEmail($medecin['email']);
            $ob_medecin->setTelephone($medecin['telephone']);
            $ob_medecin->setVille($medecin['ville']);
            $ob_medecin->setDateNaissance($medecin['date_naissance']);
            $ob_medecin->setImage($medecin['image_id']);

            foreach ($specialites as $specialite) {
                $ob_medecin->addSpecialite($specialite);
            }

            $manager->persist($ob_medecin);
        }

        $manager->flush();
    }
}
