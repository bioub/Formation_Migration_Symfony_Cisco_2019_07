# Migration Symfony 3 vers Symfony 4

## Passer à l'architecture Symfony Flex

On veut pouvoir utiliser maker-bundle et ses commandes de génération ex :
`php bin\console make:entity Partenaires\\Partenaire`

Dans Symfony Flex, le dossier src ressemble à :
```
src
├── Controller
│   ├── CompanyController.php
│   ├── ContactController.php
│   └── DefaultController.php
├── Entity
│   ├── Company.php
│   └── Contact.php
├── Form
│   └── ContactType.php
├── Kernel.php
└── Repository
    ├── CompanyRepository.php
    └── ContactRepository.php
```

Il n'y a plus de dossier `Resources` ni de dossier `Tests` (les tests sont à la racine dans `tests`, les vues à la racine dans `templates` et la config à racine dans `config`)

Actuellement notre architecture ressemble à :

```
src
└── AppBundle
    ├── AppBundle.php
    ├── Controller
    │   ├── CompanyController.php
    │   ├── ContactController.php
    │   └── DefaultController.php
    ├── Entity
    │   ├── Company.php
    │   └── Contact.php
    ├── Form
    │   └── ContactType.php
    ├── Repository
    │   ├── CompanyRepository.php
    │   └── ContactRepository.php
    ├── Resources
    │   └── views
    │       ├── Company
    │       │   ├── list.html.twig
    │       │   └── show.html.twig
    │       └── Contact
    │           ├── add.html.twig
    │           ├── delete.html.twig
    │           ├── list.html.twig
    │           ├── show.html.twig
    │           └── update.html.twig
    └── Tests
        └── Controller
            ├── CompanyControllerTest.php
            └── ContactControllerTest.php
```

Dans le cas où on a plusieurs Bundles qui aurait dû être des dossiers (et non des Bundles qui servent à créer du code réutilisable de projet en projet)

On aimerait obtenir comme arborescence :

```
src
├── Controller
│   ├── AddressBook
│   │   ├── CompanyController.php
│   │   └── ContactController.php
│   └── DefaultController.php
├── Entity
│   └── AddressBook
│       ├── Company.php
│       └── Contact.php
├── Form
│   └── AddressBook
│       └── ContactType.php
├── Kernel.php
├── Migrations
│   └── Version20190705114007.php
└── Repository
    └── AddressBook
        ├── CompanyRepository.php
        └── ContactRepository.php
```

Où les dossiers `Controller`, `Entity`, `Form`, `Repository` contiennent des sous-dossiers qui portent le nom des anciens bundles. 


### Etapes 

* Les paramètres de l'app ne sont plus dans `app/config/parameters.yml` mais soit dans : `.env`, `.env.dev` (où un autre environnement), `.env.local`, `.env.dev.local` (les fichiers `.local` ne sont pas versionnés sous Git) où sous forme de variable d'environnement (créées par le Système ou par la config Apache)

Par exemple les parametres de la base de données passent de (dans `app/config/parameters.yml` ) :
```
parameters:
    database_host: 127.0.0.1
    database_port: null
    database_name: address_book
    database_user: root
    database_password: null
```

à (dans `.env`)
```
DATABASE_URL=mysql://root@127.0.0.1:3306/address_book
```

* déplacer les fichiers, ex : `src/AddressBookBundle/Controller/ContactController.php` devient  `src/Controller/AddressBook/ContactController.php`

* refaire tous les namespaces, ex: `namespace Romain\AddressBookBundle\Controller;` devient `App\Controller\AddressBook;` (de préférence garder `App` en vendor plutôt que `Romain` pour être mieux compatible avec des bundles externes comme `maker-bundle`)

* refaire tous les uses vers les nouveaux noms de classes, ex : `Romain\AddressBookBundle\Entity\Contact` vers `App\Entity\AddressBook\Contact`, dans des IDEs comme PHPStorm ou Netbeans, ça peut se faire en supprimant les anciens uses et en activant la fonctionnalité `Source/Fix uses` (Netbeans), `Code/Optimize Imports` (PHPStorm)

* dans le dossier `Entity` refaire les annotations `ORM\Entity` lorsqu'elle contiennent une option `repositoryClass` (mettre le nouveau nom) et aussi toutes les association annotations (`ORM\ManyToOne`, `ORM\OneToMany`, `ORM\ManyToMany`, `ORM\OneToOne`) et leurs options `targetEntity`

* si les getters/setters sont basiques (set et retourne la propriété), on peut les regénérer `php bin\console make:entity AddressBook\Contact --regenerate` (remplace `doctrine:generate:entities` de Symfony 3)

* Optionnel : les controllers de Symfony 4 héritent plutôt de `Symfony\Bundle\FrameworkBundle\Controller\AbstractController` que de `Symfony\Bundle\FrameworkBundle\Controller\Controller`

* Les routes sont maintenant définies dans `config/routes.yaml`, si on souhaite conserver plusieurs fichiers de routes, il faudra les créer dans `config/routes` exemple `config/routes/address-book.yaml` (par exemple déplacer les fichiers depuis `src/AddressBookBundle/Resources/config/routes.yml`) et inclure ces fichiers dans `config/routes.yaml` avec une config de la forme :

```
 address_book:
     resource: ./routes/address-book.yaml
```

Vérifier avec `php bin\console debug:router` que toutes les routes apparaissent

* Si les routes sont définies en YAML sous forme :
```
contact_list:
    path:      /contacts/
    default: { _controller: AddressBookBundle:Contact:list }
```

les transformer en (le nom de la classe suivi de :: et du nom de la méthode, y compris Action qui est aujourd'hui optionnel) :

```
contact_list:
    path:      /contacts/
    controller: App\Controller\AddressBook\ContactController::listAction
```

* Déplacer les vues depuis le dossier `Resources/views` du bundle vers le dossier `templates` à la racine, ex : `src/AddressBookBundle/Resources/views/Contact/create.html.twig` vers `templates/address-book/contact/create.html.twig`