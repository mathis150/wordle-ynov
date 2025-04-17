# Wordle PHP

Une implémentation console du jeu Wordle en PHP avec couverture de test via PHPUnit.

## Prérequis

- PHP >= 8.0 (Pour Windows, des solutions comme [Wamp](https://www.wampserver.com/) ou [Xampp](https://www.apachefriends.org/fr/index.html) existe.)
- [PHP Composer](https://getcomposer.org/download/) installé après avoir mis en place PHP.
- [PHPUnit](https://phpunit.de/) installé globalement ou via Composer

## Exécution du projet web

Lancez votre serveur web (Wamp ou Xampp si vous êtes sous windows) et accéder à la page localhost, telle que :

```bash
http://localhost/<nom du projet>
```

## Exécution du test unit
Accédez par la console ou le terminal à la racine du projet, quand cela est fais, exécutez la commande suivante :

```bash
$ ./vendor/bin/phpunit tests
```

Dans le cas ou vous rajouteriez des fichiers de test supplémentaire (Ces derniers doivent porter le nom d'un fichier présent dans le **src**), exécutez cette commande :

```bash
$ composer dump-autoload
```

## Ce que le code gère
- Validation des mots (5 lettres, lettres uniquement)
- Feedback par lettre : correct, mal placé, incorrect
- Gestion des tentatives (6 max)
- Dictionnaire intégré
- Couverture de test > 80 %